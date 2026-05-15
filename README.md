# 🛍️ Hệ Thống Bán Hàng Online

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)
![MySQL Version](https://img.shields.io/badge/MySQL-10.4%2B-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Status](https://img.shields.io/badge/status-Active-brightgreen.svg)

> **Dự án thực hành Cơ sở Dữ liệu** - Xây dựng hệ thống bán hàng online hoàn chỉnh với giao diện người dùng, admin panel, và tích hợp multiple payment gateways.

---

## 📋 Mục Lục

- [Giới Thiệu](#-giới-thiệu)
- [Tính Năng Chính](#-tính-năng-chính)
- [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
- [Cài Đặt](#-cài-đặt)
- [Cấu Trúc Thư Mục](#-cấu-trúc-thư-mục)
- [Database](#-database)
- [Hướng Dẫn Sử Dụng](#-hướng-dẫn-sử-dụng)
- [Các Thành Viên](#-các-thành-viên)

---

## 🎯 Giới Thiệu

Hệ thống bán hàng online là một dự án toàn diện bao gồm:

✅ **Frontend**: Giao diện người dùng thân thiện, responsive  
✅ **Backend**: Xử lý logic business  
✅ **Admin Panel**: Quản lý sản phẩm, đơn hàng, khách hàng  
✅ **Payment**: Tích hợp VNPay, PayPal, COD  
✅ **Email**: Gửi thông báo xác nhận  
✅ **CMS**: Quản lý blog, banner, trang tĩnh  

---

## ⭐ Tính Năng Chính

### 👥 Chức Năng Người Dùng (9 tính năng)

| # | Tính Năng | Người Thực Hiện | Trạng Thái |
|---|-----------|-----------------|-----------|
| 1 | 🏠 Trang Chủ | LỮ VĂN TÍNH | ✅ Done |
| 2 | 👤 Quản Lý Tài Khoản | LỮ VĂN TÍNH | ✅ Done |
| 3 | 🔍 Danh Mục & Tìm Kiếm | LỮ VĂN TÍNH | ✅ Done |
| 4 | 📦 Chi Tiết Sản Phẩm | LỮ VĂN TÍNH | ✅ Done |
| 5 | 🛒 Giỏ Hàng | TẠ THÁI NGUYỄN | ✅ Done |
| 6 | 💳 Checkout | TẠ THÁI NGUYỄN | ✅ Done |
| 7 | 📋 Lịch Sử Đơn Hàng | PHAN NGUYỄN HỮU LỘC | ✅ Done |
| 8 | ⭐ Đánh Giá & Bình Luận | LÊ DU | ✅ Done |
| 9 | ❤️ Wishlist | LÊ DU | ✅ Done |

### 🔐 Chức Năng Admin (9 tính năng)

| # | Tính Năng | Người Thực Hiện | Trạng Thái |
|---|-----------|-----------------|-----------|
| 1 | 👨‍💼 Quản Lý Người Dùng | LỮ HỒ GIA HUY | ✅ Done |
| 2 | 📦 Quản Lý Sản Phẩm | LỮ HỒ GIA HUY | ✅ Done |
| 3 | 📊 Quản Lý Đơn Hàng | LỮ HỒ GIA HUY | ✅ Done |
| 4 | 👥 Quản Lý Khách Hàng | TẠ THÁI NGUYỄN | ✅ Done |
| 5 | 🎁 Khuyến Mãi & Voucher | HUỲNH VĂN HẢI | ✅ Done |
| 6 | 📝 Kiểm Duyệt Bình Luận | PHAN NGUYỄN HỮU LỘC | ✅ Done |
| 7 | 🚚 Giao Hàng & Thanh Toán | HUỲNH VĂN HẢI | ✅ Done |
| 8 | 📰 CMS (Blog & Banner) | LÊ QUỐC CƯỜNG | ✅ Done |
| 9 | 📈 Thống Kê & Báo Cáo | LÊ QUỐC CƯỜNG | ✅ Done |

---

## 🛠️ Công Nghệ Sử Dụng

| Lớp | Công Nghệ | Phiên Bản |
|-----|-----------|----------|
| **Backend** | PHP | 8.2+ |
| **Database** | MySQL/MariaDB | 10.4+ |
| **Frontend** | HTML5/CSS3/JavaScript | Vanilla |
| **Email** | PHPMailer | 7.0 |
| **Payment** | VNPay, PayPal | API |
| **Architecture** | MVC | Pattern |
| **Protection** | PDO | SQL Injection |

---

## 🖥️ Yêu Cầu Hệ Thống

| Yêu Cầu | Phiên Bản |
|---------|----------|
| PHP | 8.2+ |
| MySQL | 10.4+ |
| Apache | 2.4+ |
| RAM | 2GB |
| Disk | 500MB |

---

## ⚙️ Cài Đặt

### Bước 1: Download Dự Án
```bash
git clone https://github.com/lequoccuong141/web-shop-php.git
cd web-shop-php
```

### Bước 2: Di Chuyển vào XAMPP
```bash
cp -r . /xampp/htdocs/web-shop-php/
```

### Bước 3: Cài Đặt Database
```bash
# Truy cập phpMyAdmin
http://localhost/phpmyadmin

# Tạo database: web_shop_php
# Import: sql/database/web_shop_php.sql
```

### Bước 4: Cấu Hình
```bash
cp config.example.php config.php
# Sửa: MAIL_USERNAME, MAIL_PASSWORD, VNP_*, PAYPAL_*
```

### Bước 5: Chạy Ứng Dụng
```
http://localhost/web-shop-php/
```

### Tài Khoản Demo
```
Admin:
- Email: admin@example.com
- Password: admin123

Customer:
- Email: customer@example.com
- Password: customer123
```

---

## 📁 Cấu Trúc Thư Mục

```
web-shop-php/
├── public/
│   └── index.php                 # Entry point
├── app/
│   ├── controller.php            # Frontend Controller
│   ├── CartController.php        # Giỏ hàng
│   ├── CheckoutController.php    # Thanh toán
│   ├── PaymentController.php     # Xử lý payment
│   ├── Database.php              # Kết nối DB
│   ├── EmailService.php          # Email service
│   └── Admin/                    # Admin controllers
├── views/                        # Templates
├── components/                   # Reusable components
├── src/styles/                   # CSS
├── asset/                        # Images
├── sql/database/                 # SQL dump
├── config.php                    # Configuration
├── composer.json                 # Dependencies
└── README.md                     # This file
```

---

## 🗄️ Database

### 21 Bảng Chính
```
users, user_address, products, product_images,
product_variants, variant_attributes, categories,
manufacturers, inventory, carts, orders, order_items,
payments, vouchers, voucher_categories,
voucher_products, reviews, favority, banner,
blogs, pages
```

---

## 📖 Hướng Dẫn Sử Dụng

### Khách Hàng
```
1. Truy cập: http://localhost/web-shop-php/
2. Đăng ký / Đăng nhập
3. Duyệt sản phẩm
4. Thêm vào giỏ hàng
5. Thanh toán
6. Theo dõi đơn hàng
```

### Admin
```
URL: http://localhost/web-shop-php/public/?action=admin_dashboard
Email: admin@example.com
Password: admin123

Quản lý:
- Sản phẩm
- Đơn hàng
- Khách hàng
- Khuyến mãi
- Báo cáo
- CMS
```

---

## 👥 Các Thành Viên

| MSSV | Họ Tên | Vai Trò | Chức Năng |
|------|--------|---------|-----------|
| 227060035 | Nguyễn Thái Dương | Frontend & Backend | Trang chủ, Tài khoản, Danh mục, Chi tiết |
| 227060015 | Sơn Hoàng Nhân | Backend | Support |
| 227060059 | Trần Phú Quốc | Backend | Support |
| 227060169 | Nguyễn Văn Hoài Tâm | Backend | Support |
| 227060026 | Nguyễn Vũ Trạng | Backend | Support |
| **227060055** | **Lê Quốc Cường** | **Trưởng Nhóm** | **CMS, Báo Cáo, Project Manager** |
| 227060014 | Danh Dương Bình Yên | Backend | Support |

---

## ✅ Ưu Điểm
- ✅ Kiến trúc MVC rõ ràng
- ✅ PDO - ngăn chặn SQL Injection
- ✅ Multiple payment gateways
- ✅ Email confirmation
- ✅ Responsive design
- ✅ Full admin panel
- ✅ CMS blog + pages
- ✅ Inventory tracking

## ⚠️ Nhược Điểm
- ❌ Routing GET parameter (không REST)
- ❌ Không framework Laravel/Symfony
- ❌ Bộ lọc sản phẩm chưa đủ
- ❌ Không có unit tests
- ❌ API chưa RESTful

---

## 📞 Liên Hệ

**Trưởng Nhóm: Lê Quốc Cường**
- Email: lequoccuong141@gmail.com
- Điện Thoại: 03731141939
- Giờ Hỗ Trợ: Tối 2, 4, 6 + Sáng Chủ Nhật

---

## 📚 Tài Liệu

- [Báo Cáo Chi Tiết](BAO_CAO_CHI_TIET_CHU_NANG_CAPNHAT.xlsx)
- [Tổng Hợp Công Nghệ](TONG_HOP_CONG_NGHE_CAPNHAT.xlsx)
- [Báo Cáo HTML](BAO_CAO_HE_THONG.html)

---

<div align="center">

**Made with ❤️ by Nhóm siêu nhân hồng**

Cập nhật: 15/05/2026

</div>
