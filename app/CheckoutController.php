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
            SELECT c.quantity, c.variant_id, p.product_id, p.name, p.price, p.discount_price, p.thumbnail,
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

        $recipient_name = $_POST['recipient_name'] ?? '';
        $recipient_phone = $_POST['recipient_phone'] ?? '';
        $recipient_email = $_POST['recipient_email'] ?? '';
        $province_name = $_POST['province_name'] ?? '';
        $district_name = $_POST['district_name'] ?? '';
        $ward_name = $_POST['ward_name'] ?? '';
        $specific_address = $_POST['specific_address'] ?? '';
        
        $shipping_method = $_POST['shipping_method'] ?? 'standard';
        $payment_method = $_POST['payment_method'] ?? 'cod';

        // Calculate totals
        $stmtItems = $this->db->prepare("
            SELECT c.quantity, c.variant_id, p.product_id, p.name, p.price, p.discount_price, p.thumbnail,
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

        $shipping_fee = 0;
        if ($shipping_method === 'standard') $shipping_fee = 30000;
        else if ($shipping_method === 'fast') $shipping_fee = 50000;
        else if ($shipping_method === 'pickup') $shipping_fee = 0;

        $total_amount = $subtotal + ($subtotal * 0.10) + $shipping_fee;

        $order_code = 'ORD' . date('YmdHis') . rand(100, 999);

        // Begin Transaction
        $this->db->beginTransaction();

        try {
            // Create Order
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, order_code, status, payment_status, subtotal, shipping_fee, total_amount, 
                    recipient_name, recipient_phone, province_name, district_name, ward_name, specific_address
                ) VALUES (
                    :user_id, :order_code, 'pending', 'unpaid', :subtotal, :shipping_fee, :total_amount,
                    :recipient_name, :recipient_phone, :province_name, :district_name, :ward_name, :specific_address
                )
            ");

            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'order_code' => $order_code,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping_fee,
                'total_amount' => $total_amount,
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

            foreach ($cartItems as $item) {
                $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']);
                $total_price = $price * $item['quantity'];
                
                // Lưu tên sản phẩm kèm thông tin biến thể để làm snapshot dữ liệu
                $product_name = $item['name'];
                if (!empty($item['variant_details'])) {
                    $product_name .= ' (' . $item['variant_details'] . ')';
                }

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

                header("Location: index.php?action=checkout_success&order_code=" . $order_code);
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
                "return_url" => "http://localhost/webshopphp/public/index.php?action=paypal_return&order_id=" . $order_id,
                "cancel_url" => "http://localhost/webshopphp/public/index.php?action=checkout_failed"
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
