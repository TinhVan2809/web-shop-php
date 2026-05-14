<?php

require_once PROJECT_ROOT . '/app/Database.php';
require_once PROJECT_ROOT . '/app/CartController.php';
require_once PROJECT_ROOT . '/config.php';
require_once PROJECT_ROOT . '/app/EmailService.php';

class CheckoutController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login&redirect=checkout");
            exit;
        }

        $cartCtrl = new CartController();
        $cartCount = $cartCtrl->getCartCount();

        if ($cartCount === 0) {
            header("Location: index.php?action=cart");
            exit;
        }

        // Fetch user data
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate subtotal
        $stmtItems = $this->db->prepare("
            SELECT c.quantity, c.variant_id, p.product_id, p.category_id, p.name, p.price, p.discount_price, p.thumbnail,
                   pv.sku, pv.price as variant_price,
                   GROUP_CONCAT(CONCAT(va.attribute_name, ': ', va.attribute_value) SEPARATOR ', ') as variant_details
            FROM carts c 
            JOIN products p ON c.product_id = p.product_id 
            LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id
            LEFT JOIN variant_attributes va ON pv.variant_id = va.variant_id
            WHERE c.user_id = :user_id
            GROUP BY c.cart_id
        ");
        $stmtItems->execute(['user_id' => $_SESSION['user_id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $subtotal = 0;
        foreach ($items as $item) {
            $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
            $subtotal += $price * $item['quantity'];
        }

        $tax = $subtotal * 0.10;
        $voucher_code = strtoupper(trim($_GET['voucher'] ?? ''));
        $discount_amount = 0;

        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/checkout.php';
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=checkout");
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $recipient_name = trim($_POST['recipient_name'] ?? '');
        $recipient_phone = trim($_POST['recipient_phone'] ?? '');
        $recipient_email = trim($_POST['recipient_email'] ?? '');
        $province_name = trim($_POST['province_name'] ?? '');
        $district_name = trim($_POST['district_name'] ?? '');
        $ward_name = trim($_POST['ward_name'] ?? '');
        $specific_address = trim($_POST['specific_address'] ?? '');

        if (
            $recipient_name === '' ||
            $recipient_phone === '' ||
            $recipient_email === '' ||
            $province_name === '' ||
            $district_name === '' ||
            $ward_name === '' ||
            $specific_address === ''
        ) {
            header("Location: index.php?action=checkout&error=" . urlencode('Vui lòng nhập đầy đủ thông tin giao hàng.'));
            exit;
        }

        $shipping_method = $_POST['shipping_method'] ?? 'standard';
        $payment_method = $_POST['payment_method'] ?? 'cod';
        $voucher_code = strtoupper(trim($_POST['voucher_code'] ?? ''));

        // Calculate totals
        $stmtItems = $this->db->prepare("
            SELECT c.quantity, c.variant_id, p.product_id, p.category_id, p.name, p.price, p.discount_price, p.thumbnail,
                   pv.sku, pv.price as variant_price,
                   GROUP_CONCAT(CONCAT(va.attribute_name, ': ', va.attribute_value) SEPARATOR ', ') as variant_details
            FROM carts c 
            JOIN products p ON c.product_id = p.product_id 
            LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id
            LEFT JOIN variant_attributes va ON pv.variant_id = va.variant_id
            WHERE c.user_id = :user_id
            GROUP BY c.cart_id
        ");
        $stmtItems->execute(['user_id' => $_SESSION['user_id']]);
        $cartItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            header("Location: index.php?action=cart");
            exit;
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
            $subtotal += $price * $item['quantity'];
        }

        $discount_amount = 0;
        $voucher_id = null;
        $voucher_discount = null;

        if ($voucher_code !== '') {
            $voucherResult = $this->resolveVoucher($voucher_code, $cartItems, $subtotal);
            if (isset($voucherResult['error'])) {
                header("Location: index.php?action=checkout&error=" . urlencode($voucherResult['error']) . "&voucher=" . urlencode($voucher_code));
                exit;
            }

            $discount_amount = $voucherResult['discount'];
            $voucher_id = $voucherResult['voucher']['voucher_id'];
            $voucher_discount = $discount_amount;
        }

        $shipping_fee = 0;
        if ($shipping_method === 'standard') $shipping_fee = 30000;
        else if ($shipping_method === 'fast') $shipping_fee = 50000;
        else if ($shipping_method === 'pickup') $shipping_fee = 0;

        $total_amount = $subtotal + ($subtotal * 0.10) + $shipping_fee - $discount_amount;
        if ($total_amount < 0) {
            $total_amount = 0;
        }

        $order_code = 'ORD' . date('YmdHis') . rand(100, 999);

        // Begin Transaction
        $this->db->beginTransaction();

        try {
            // Create Order
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, order_code, status, payment_status, subtotal, discount_amount, shipping_fee, total_amount,
                    voucher_id, voucher_code, voucher_discount, recipient_name, recipient_phone, province_name,
                    district_name, ward_name, specific_address
                ) VALUES (
                    :user_id, :order_code, 'pending', 'unpaid', :subtotal, :discount_amount, :shipping_fee, :total_amount,
                    :voucher_id, :voucher_code, :voucher_discount, :recipient_name, :recipient_phone, :province_name,
                    :district_name, :ward_name, :specific_address
                )
            ");

            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'order_code' => $order_code,
                'subtotal' => $subtotal,
                'discount_amount' => $discount_amount,
                'shipping_fee' => $shipping_fee,
                'total_amount' => $total_amount,
                'voucher_id' => $voucher_id,
                'voucher_code' => $voucher_code !== '' ? $voucher_code : null,
                'voucher_discount' => $voucher_discount,
                'recipient_name' => $recipient_name,
                'recipient_phone' => $recipient_phone,
                'province_name' => $province_name,
                'district_name' => $district_name,
                'ward_name' => $ward_name,
                'specific_address' => $specific_address
            ]);

            $order_id = $this->db->lastInsertId();

            // Insert Order Items
            $stmtItem = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, variant_id, product_name, product_image, sku, price, quantity, total_price)
                VALUES (:order_id, :product_id, :variant_id, :product_name, :product_image, :sku, :price, :quantity, :total_price)
            ");

            // 1. Chuẩn bị câu lệnh trừ kho
            // Sử dụng <=> để so sánh variant_id (xử lý được cả trường hợp NULL)
            // Kiểm tra available_quantity để đảm bảo tính cả lượng hàng đang bị "giữ chỗ"
            $stmtUpdateStock = $this->db->prepare("
                UPDATE inventory 
                SET quantity = quantity - :qty 
                WHERE product_id = :pid AND variant_id <=> :vid AND available_quantity >= :qty
            ");

            foreach ($cartItems as $item) {
                $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
                $total_price = $price * $item['quantity'];

                // Lưu tên sản phẩm kèm thông tin biến thể để làm snapshot dữ liệu
                $product_name = $item['name'];
                if (!empty($item['variant_details'])) {
                    $product_name .= ' (' . $item['variant_details'] . ')';
                }

                // 2. Thực hiện trừ tồn kho ngay trong transaction
                $stmtUpdateStock->execute([
                    'qty' => $item['quantity'],
                    'pid' => $item['product_id'],
                    'vid' => $item['variant_id']
                ]);

                // Nếu rowCount bằng 0, nghĩa là điều kiện WHERE không thỏa mãn (Hết hàng hoặc sai ID)
                if ($stmtUpdateStock->rowCount() === 0) {
                    $item_label = $item['name'];
                    if (!empty($item['variant_details'])) {
                        $item_label .= ' (' . $item['variant_details'] . ')';
                    }
                    throw new Exception("Sản phẩm '{$item_label}' hiện không đủ số lượng trong kho.");
                }

                // 3. Thêm vào bảng order_items (Snapshot dữ liệu)

                $stmtItem->execute([
                    'order_id' => $order_id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $product_name,
                    'product_image' => $item['thumbnail'],
                    'sku' => $item['sku'],
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'total_price' => $total_price
                ]);
            }

            if ($voucher_id) {
                $stmtVoucher = $this->db->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE voucher_id = :id");
                $stmtVoucher->execute(['id' => $voucher_id]);
            }

            // Clear Cart
            $stmtClear = $this->db->prepare("DELETE FROM carts WHERE user_id = :user_id");
            $stmtClear->execute(['user_id' => $_SESSION['user_id']]);

            // Update user email if provided
            if (!empty($recipient_email)) {
                $stmtEmail = $this->db->prepare("UPDATE users SET gmail = :email WHERE user_id = :id");
                $stmtEmail->execute(['email' => $recipient_email, 'id' => $_SESSION['user_id']]);
            }

            $this->db->commit();

            // Redirect to Payment Gateways
            if ($payment_method === 'cod') {
                // Send Email
                $emailService = new EmailService();
                $emailService->sendOrderConfirmation($order_id);

                header("Location: index.php?action=checkout_success&order_code=" . $order_code . "&order_id=" . $order_id);
                exit;
            } else if ($payment_method === 'vnpay') {
                $this->processVNPay($order_id, $order_code, $total_amount);
            } else if ($payment_method === 'paypal') {
                $this->processPayPal($order_id, $order_code, $total_amount);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            header("Location: index.php?action=checkout_failed&error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function applyVoucher()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để áp dụng voucher.']);
            exit;
        }

        $voucher_code = strtoupper(trim($_POST['voucher_code'] ?? ''));
        if ($voucher_code === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã voucher.']);
            exit;
        }

        $stmtItems = $this->db->prepare("\n            SELECT c.quantity, c.variant_id, p.product_id, p.category_id, p.name, p.price, p.discount_price, p.thumbnail,\n                   pv.sku, pv.price as variant_price,\n                   GROUP_CONCAT(CONCAT(va.attribute_name, ': ', va.attribute_value) SEPARATOR ', ') as variant_details\n            FROM carts c \n            JOIN products p ON c.product_id = p.product_id \n            LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id\n            LEFT JOIN variant_attributes va ON pv.variant_id = va.variant_id\n            WHERE c.user_id = :user_id\n            GROUP BY c.cart_id\n        ");
        $stmtItems->execute(['user_id' => $_SESSION['user_id']]);
        $cartItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống.']);
            exit;
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
            $subtotal += $price * $item['quantity'];
        }

        $voucherResult = $this->resolveVoucher($voucher_code, $cartItems, $subtotal);
        if (isset($voucherResult['error'])) {
            echo json_encode(['success' => false, 'message' => $voucherResult['error']]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'discount' => $voucherResult['discount'],
            'voucher_code' => $voucher_code,
            'discount_type' => $voucherResult['voucher']['discount_type'] ?? 'fixed',
            'discount_value' => (float)($voucherResult['voucher']['discount_value'] ?? 0),
        ]);
        exit;
    }

    private function resolveVoucher(string $code, array $cartItems, float $subtotal): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE code = ? LIMIT 1");
        $stmt->execute([$code]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$voucher) {
            return ['error' => 'Voucher không tồn tại.'];
        }

        if (($voucher['status'] ?? 'inactive') !== 'active') {
            return ['error' => 'Voucher không còn hoạt động.'];
        }

        $now = new DateTime('now');
        if (!empty($voucher['start_date']) && $now < new DateTime($voucher['start_date'])) {
            return ['error' => 'Voucher chưa bắt đầu.'];
        }
        if (!empty($voucher['end_date']) && $now > new DateTime($voucher['end_date'])) {
            return ['error' => 'Voucher đã hết hạn.'];
        }

        if ($voucher['usage_limit'] !== null && (int)$voucher['used_count'] >= (int)$voucher['usage_limit']) {
            return ['error' => 'Voucher đã hết lượt sử dụng.'];
        }

        if ($subtotal < (float)$voucher['min_order_value']) {
            return ['error' => 'Đơn hàng chưa đạt giá trị tối thiểu để dùng voucher.'];
        }

        $productStmt = $this->db->prepare("SELECT product_id FROM voucher_products WHERE voucher_id = ?");
        $productStmt->execute([$voucher['voucher_id']]);
        $productIds = array_map('intval', $productStmt->fetchAll(PDO::FETCH_COLUMN));

        $categoryStmt = $this->db->prepare("SELECT category_id FROM voucher_categories WHERE voucher_id = ?");
        $categoryStmt->execute([$voucher['voucher_id']]);
        $categoryIds = array_map('intval', $categoryStmt->fetchAll(PDO::FETCH_COLUMN));

        $eligibleSubtotal = 0.0;
        foreach ($cartItems as $item) {
            $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
            $lineTotal = $price * $item['quantity'];

            $isEligible = true;
            if (!empty($productIds)) {
                $isEligible = in_array((int)$item['product_id'], $productIds, true);
            } else if (!empty($categoryIds)) {
                $isEligible = in_array((int)$item['category_id'], $categoryIds, true);
            }

            if ($isEligible) {
                $eligibleSubtotal += $lineTotal;
            }
        }

        if ($eligibleSubtotal <= 0) {
            return ['error' => 'Voucher không áp dụng cho sản phẩm trong giỏ hàng.'];
        }

        $discount = 0.0;
        if (($voucher['discount_type'] ?? 'percent') === 'percent') {
            $discount = $eligibleSubtotal * ((float)$voucher['discount_value'] / 100);
        } else {
            $discount = (float)$voucher['discount_value'];
        }

        if (!empty($voucher['max_discount']) && $discount > (float)$voucher['max_discount']) {
            $discount = (float)$voucher['max_discount'];
        }

        if ($discount > $eligibleSubtotal) {
            $discount = $eligibleSubtotal;
        }

        return [
            'voucher' => $voucher,
            'discount' => $discount,
        ];
    }

    private function processVNPay($order_id, $order_code, $total_amount)
    {
        $vnp_TmnCode = VNP_TMN_CODE;
        $vnp_HashSecret = VNP_HASH_SECRET;
        $vnp_Url = VNP_URL;
        $vnp_Returnurl = VNP_RETURN_URL;

        $vnp_TxnRef = $order_id . '_' . time(); // Avoid duplicate processing
        $vnp_OrderInfo = "Thanh toan don hang " . $order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $total_amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret) && trim($vnp_HashSecret) !== '') {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        exit;
    }

    private function processPayPal($order_id, $order_code, $total_amount)
    {
        // Simple REST implementation to get Approval URL
        $clientId = PAYPAL_CLIENT_ID;
        $secret = PAYPAL_SECRET;

        $environment = PAYPAL_MODE === 'sandbox' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        // 1. Get Access Token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$environment/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result = curl_exec($ch);

        if (empty($result)) {
            header("Location: index.php?action=checkout_failed&error=PayPal token error");
            exit;
        }

        $json = json_decode($result);
        $accessToken = $json->access_token ?? '';
        curl_close($ch);

        if (!$accessToken) {
            header("Location: index.php?action=checkout_failed&error=Invalid PayPal Credentials");
            exit;
        }

        // 2. Create Order
        $amount_usd = number_format($total_amount / 25000, 2, '.', ''); // Convert VND to USD mock rate 25000

        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "reference_id" => (string)$order_id,
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $amount_usd
                    ],
                    "description" => "Haseki Store Order " . $order_code
                ]
            ],
            "application_context" => [
                "return_url" => "http://localhost/web-shop-php/public/index.php?action=paypal_return&order_id=" . $order_id,
                "cancel_url" => "http://localhost/web-shop-php/public/index.php?action=checkout_failed"
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$environment/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        curl_close($ch);

        $order = json_decode($result);

        if (isset($order->links)) {
            foreach ($order->links as $link) {
                if ($link->rel === 'approve') {
                    header("Location: " . $link->href);
                    exit;
                }
            }
        }

        header("Location: index.php?action=checkout_failed&error=Cannot create PayPal order");
        exit;
    }
}
