### Run Client and Server (Nhập lên trình duyệt)
```
http://localhost/web-shop-php/public/index.php
```

###  Tài khoản Admin
```bash
    admin
    1
```

### Tài khoản khách hàng
```bash
    username12345
    12345678
```
###

![](/asset/Screenshot%202026-04-27%20103946.png)

### Các lỗi thường gặp
**Page not found**
=> Kiểm tra lại URL (Mặc định: http://localhost/web-shop-php/public/index.php)

**Not found coulunm**
=> Không tìm thấy bảng trong database. Kiểm tra lại đã import đúng database chưa, chú ý tên database (web_shop_php) 

**Page Crash** 
=> Bật Xampp lên

**Lỗi file CheckoutController.php dòng 16 hoặc dòng 5**
=> Kiểm tra cấu hình file [config](/config.example.php) (Liên hệ người giữ repo gốc)

Đọc thêm [tại đây](./docs/doc.md)


### Tải khoản Test Thanh toán Sandbox
Để test chức năng thanh toán trực tuyến, vui lòng sử dụng các tài khoản môi trường Sandbox sau:

**1. VNPay Sandbox:**
- **Ngân hàng:** NCB
- **Số thẻ:** 9704198526191432198
- **Tên chủ thẻ:** NGUYEN VAN A
- **Ngày phát hành:** 07/15
- **Mật khẩu OTP:** 123456

**2. PayPal Sandbox:**
- **Email:** sb-x3m3g30000000@personal.example.com (Vui lòng thay thế bằng email Buyer Sandbox của bạn nếu có)
- **Password:** :!RO]ww1

**3. Email Test (Nhận hóa đơn):**
- **Email:** tathainguyen24@gmail.com
- *(Hoặc bạn có thể nhập email thật của bạn ở bước thanh toán để kiểm tra hộp thư đến).*
