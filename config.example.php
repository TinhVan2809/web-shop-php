<?php
// config.php
// Cấu hình thông tin để gửi email
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', 'tathainguyen24@gmail.com'); // ĐIỀN EMAIL CỦA BẠN VÀO ĐÂY
define('MAIL_PASSWORD', 'kbthtyfwacyfodgx'); // ĐIỀN MẬT KHẨU ỨNG DỤNG (16 KÝ TỰ) VÀO ĐÂY
define('MAIL_PORT', 587); // Hoặc 465
define('MAIL_FROM_ADDRESS', 'tathainguyen24@gmail.com'); // ĐIỀN EMAIL CỦA BẠN VÀO ĐÂY
define('MAIL_FROM_NAME', 'Haseki Store');

// VNPay Configuration
define('VNP_TMN_CODE', 'ATGY6X7Q'); 
define('VNP_HASH_SECRET', '3RW7EOU7SB8GAKMA4KE6OPHCQD76LSP0'); 
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define('VNP_RETURN_URL', 'http://localhost/webshopphp/public/index.php?action=vnpay_return');

// PayPal Configuration
define('PAYPAL_CLIENT_ID', 'AYZxbmnDc2uJaWTV9zON7VK_Lu6GkEMbfdc5qEWYxo-alEh130MlBGMqXdJfVipJuDVL5jAVbcp4MtQk'); 
define('PAYPAL_SECRET', 'ECkZsEBQDr66k2_UMHUvblJFLGojbdforOXZz3HdZ-0j7eGoP2IDfoKvK_vT8IWhzkwqDdp3YbI7MFiw'); 
define('PAYPAL_MODE', 'sandbox');