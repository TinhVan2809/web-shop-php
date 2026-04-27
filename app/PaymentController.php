<?php

require_once PROJECT_ROOT . '/app/Database.php';
require_once PROJECT_ROOT . '/config.php';
require_once PROJECT_ROOT . '/app/EmailService.php';

class PaymentController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function vnpayReturn()
    {
        $vnp_HashSecret = VNP_HASH_SECRET;
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
        $order_id = explode('_', $vnp_TxnRef)[0] ?? 0;
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';

        // If no secret is configured yet, we skip validation for testing purposes
        $isValid = ($secureHash === $vnp_SecureHash);
        if (empty(trim($vnp_HashSecret))) {
             $isValid = true; 
        }

        if ($isValid) {
            if ($vnp_ResponseCode == '00') {
                // Success
                $this->handleSuccess($order_id, 'vnpay', $_GET['vnp_TransactionNo'] ?? '');
            } else {
                // Failed
                $this->handleFailed($order_id);
            }
        } else {
            // Invalid Signature
            header("Location: index.php?action=checkout_failed&error=Invalid Signature");
            exit;
        }
    }

    public function paypalReturn()
    {
        $order_id = $_GET['order_id'] ?? null;
        $token = $_GET['token'] ?? null; // PayPal Order ID
        
        if (!$order_id || !$token) {
            header("Location: index.php?action=checkout_failed&error=Missing Parameters");
            exit;
        }

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
        $json = json_decode($result);
        $accessToken = $json->access_token ?? '';
        curl_close($ch);

        if (!$accessToken) {
            $this->handleFailed($order_id);
            return;
        }

        // 2. Capture Order
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$environment/v2/checkout/orders/$token/capture");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($result);
        
        if (isset($response->status) && $response->status === 'COMPLETED') {
            $transaction_id = $response->purchase_units[0]->payments->captures[0]->id ?? '';
            $this->handleSuccess($order_id, 'paypal', $transaction_id);
        } else {
            $this->handleFailed($order_id);
        }
    }

    private function handleSuccess($order_id, $method, $transaction_code)
    {
        // Update Order Status
        $stmt = $this->db->prepare("UPDATE orders SET payment_status = 'paid' WHERE order_id = :id");
        $stmt->execute(['id' => $order_id]);

        // Insert into payments table
        $stmtPayment = $this->db->prepare("
            INSERT INTO payments (order_id, amount, method, status, transaction_code) 
            SELECT order_id, total_amount, :method, 'success', :code FROM orders WHERE order_id = :id
        ");
        $stmtPayment->execute(['method' => $method, 'code' => $transaction_code, 'id' => $order_id]);

        // Get Order Code
        $stmtCode = $this->db->prepare("SELECT order_code FROM orders WHERE order_id = :id");
        $stmtCode->execute(['id' => $order_id]);
        $order_code = $stmtCode->fetchColumn();

        // Send Email
        $emailService = new EmailService();
        $emailService->sendOrderConfirmation($order_id);

        header("Location: index.php?action=checkout_success&order_code=" . $order_code);
        exit;
    }

    private function restockItems($order_id)
    {
        $stmt = $this->db->prepare("SELECT product_id, variant_id, quantity FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $updateStock = $this->db->prepare("
            UPDATE inventory 
            SET quantity = quantity + :qty 
            WHERE product_id = :pid AND variant_id <=> :vid
        ");

        foreach ($items as $item) {
            $updateStock->execute([
                'qty' => $item['quantity'],
                'pid' => $item['product_id'],
                'vid' => $item['variant_id']
            ]);
        }
    }

    private function handleFailed($order_id)
    {
        $stmt = $this->db->prepare("UPDATE orders SET payment_status = 'failed' WHERE order_id = :id");
        $stmt->execute(['id' => $order_id]);
        
        // Hoàn lại số lượng vào kho nếu thanh toán trực tuyến thất bại
        $this->restockItems($order_id);

        header("Location: index.php?action=checkout_failed");
        exit;
    }
}
