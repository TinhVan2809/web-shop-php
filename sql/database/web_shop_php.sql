-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 25, 2026 lúc 05:24 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_shop_php`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `add_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `product_id`, `add_at`) VALUES
(1, 3, 2, '2026-04-22 18:00:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `create_at` timestamp NULL DEFAULT current_timestamp(),
  `url` varchar(255) DEFAULT NULL COMMENT 'Duong link trang web nha san xuat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `create_at`, `url`) VALUES
(1, 'shoes', '2026-04-21 03:34:07', NULL),
(2, 'shirt', '2026-04-21 09:33:46', NULL),
(3, 'pants', '2026-04-21 09:33:46', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `reserved_quantity` int(11) DEFAULT 0 COMMENT 'so luong da giu cho (don hang chua hoan tat)',
  `available_quantity` int(11) GENERATED ALWAYS AS (`quantity` - `reserved_quantity`) STORED COMMENT 'so luong co the ban (ton - giu cho)',
  `min_stock_level` int(11) DEFAULT 10 COMMENT 'muc ton toi thieu (canh bao sap het hang)',
  `status` varchar(50) DEFAULT 'in_stock',
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `variant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity`, `reserved_quantity`, `min_stock_level`, `status`, `last_updated`, `variant_id`) VALUES
(1, 1, 50, 5, 10, 'in_stock', '2026-04-21 10:14:58', 1),
(2, 1, 40, 2, 10, 'in_stock', '2026-04-21 10:14:58', 2),
(3, 1, 30, 0, 10, 'in_stock', '2026-04-21 10:14:58', 3),
(4, 1, 20, 1, 10, 'low_stock', '2026-04-21 10:14:58', 4),
(5, 2, 25, 3, 5, 'in_stock', '2026-04-21 10:14:58', 5),
(6, 2, 15, 5, 5, 'low_stock', '2026-04-21 10:14:58', 6),
(7, 2, 10, 2, 5, 'low_stock', '2026-04-21 10:14:58', 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `manufacturers`
--

CREATE TABLE `manufacturers` (
  `manufacturer_id` int(11) NOT NULL,
  `manufacturer_name` varchar(255) NOT NULL,
  `create_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `manufacturers`
--

INSERT INTO `manufacturers` (`manufacturer_id`, `manufacturer_name`, `create_at`) VALUES
(1, 'Nike', '2026-04-21 09:24:52'),
(2, 'Adidas', '2026-04-21 09:35:55'),
(3, 'Puma', '2026-04-21 09:35:55'),
(4, 'Under Armour', '2026-04-21 09:35:55'),
(5, 'New Balance', '2026-04-21 09:35:55'),
(6, 'Lululemon', '2026-04-21 09:35:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL COMMENT 'ID đơn hàng',
  `user_id` int(11) NOT NULL COMMENT 'ID người dùng đặt hàng',
  `order_code` varchar(50) DEFAULT NULL COMMENT 'Mã đơn hàng hiển thị cho user (VD: ORD20260420001)',
  `status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending' COMMENT 'Trạng thái đơn hàng: pending(chờ), confirmed(xác nhận), shipping(đang giao), completed(hoàn thành), cancelled(đã hủy)',
  `payment_status` enum('unpaid','paid','failed','refunded') DEFAULT 'unpaid' COMMENT 'Trạng thái thanh toán: unpaid(chưa thanh toán), paid(đã thanh toán), failed(thất bại), refunded(đã hoàn tiền)',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'Tổng tiền sản phẩm trước khi giảm giá và phí ship',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Số tiền được giảm từ voucher hoặc khuyến mãi',
  `shipping_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'Phí vận chuyển',
  `total_amount` decimal(15,2) NOT NULL COMMENT 'Tổng tiền cuối cùng khách phải trả = subtotal - discount + shipping_fee',
  `voucher_id` int(11) DEFAULT NULL COMMENT 'ID voucher đã sử dụng (có thể NULL nếu không dùng)',
  `voucher_code` varchar(50) DEFAULT NULL COMMENT 'Snapshot mã voucher tại thời điểm đặt hàng',
  `voucher_discount` decimal(10,2) DEFAULT NULL COMMENT 'Snapshot số tiền giảm từ voucher',
  `recipient_name` varchar(255) NOT NULL COMMENT 'Tên người nhận (snapshot, không phụ thuộc user_address)',
  `recipient_phone` varchar(20) NOT NULL COMMENT 'Số điện thoại người nhận',
  `province_name` varchar(100) NOT NULL COMMENT 'Tên tỉnh/thành (snapshot)',
  `district_name` varchar(100) NOT NULL COMMENT 'Tên quận/huyện (snapshot)',
  `ward_name` varchar(100) NOT NULL COMMENT 'Tên phường/xã (snapshot)',
  `specific_address` varchar(255) NOT NULL COMMENT 'Địa chỉ cụ thể: số nhà, đường, khu dân cư...',
  `user_address_id` int(11) DEFAULT NULL COMMENT 'ID địa chỉ của user (chỉ dùng tham chiếu/gợi ý, không dùng hiển thị)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Thời điểm tạo đơn hàng',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời điểm cập nhật gần nhất (trạng thái, thanh toán,...)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu thông tin đơn hàng: người mua, địa chỉ giao, trạng thái, thanh toán và tổng tiền (đã snapshot để đảm bảo tính toàn vẹn dữ liệu)';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL COMMENT 'ID chi tiết đơn hàng',
  `order_id` int(11) NOT NULL COMMENT 'Liên kết đến đơn hàng',
  `product_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `variant_id` int(11) DEFAULT NULL COMMENT 'Biến thể (size, màu)',
  `product_name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm tại thời điểm mua',
  `product_image` varchar(255) DEFAULT NULL COMMENT 'Ảnh sản phẩm snapshot',
  `sku` varchar(100) DEFAULT NULL COMMENT 'Mã biến thể',
  `price` decimal(15,2) NOT NULL COMMENT 'Giá tại thời điểm mua',
  `quantity` int(11) NOT NULL COMMENT 'Số lượng',
  `total_price` decimal(15,2) NOT NULL COMMENT 'price * quantity'
) ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` varchar(255) DEFAULT NULL COMMENT 'tien da thanh toan',
  `method` varchar(255) DEFAULT NULL COMMENT 'momo, cod, banking',
  `status` varchar(255) DEFAULT NULL COMMENT 'pending, success, failed',
  `transaction_code` varchar(255) DEFAULT NULL COMMENT 'ma giao dich',
  `paid_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ngay giao dich'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL COMMENT 'giam gia (neu co)',
  `sku` varchar(100) DEFAULT NULL COMMENT 'ma san pham',
  `category_id` int(11) DEFAULT NULL COMMENT 'loai san pham (ao, quan, giay,..)',
  `manufacturer_id` int(11) DEFAULT NULL COMMENT 'hang san xuat',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Anh chinh',
  `sold_count` int(11) DEFAULT 0 COMMENT 'so luong da ban',
  `is_new` tinyint(1) DEFAULT 1,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `short_description`, `price`, `discount_price`, `sku`, `category_id`, `manufacturer_id`, `thumbnail`, `sold_count`, `is_new`, `status`, `created_at`, `updated_at`) VALUES
(1, ' Pickleball NikeCourt Air Zoom Vapor 11', NULL, 'Giầy thể thao cho nam', 2950000.00, 1500000.00, '123425', 1, NULL, 'nikecourt-air-zoom-vapor-11-mens-hard-court-tennis-shoes-03_720x720xcrop-preview.png', 0, 1, 'active', '2026-04-21 03:31:35', '2026-04-23 01:25:12'),
(2, 'Nike Air Zoom Pegasus 40', 'Giày chạy bộ cao cấp với công nghệ Zoom Air, phù hợp cho luyện tập và thi đấu.', 'Giày chạy bộ Nike', 3200000.00, 2900000.00, 'NIKE-PEG40', 1, 1, 'nike_pegasus_40.jpg', 120, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(3, 'Adidas Ultraboost 22', 'Giày chạy bộ với đế Boost êm ái, hoàn trả năng lượng tốt.', 'Giày chạy Adidas', 3500000.00, 3100000.00, 'ADI-UB22', 1, 2, 'adidas_ultraboost_22.jpg', 95, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(4, 'Puma Training T-Shirt', 'Áo thun thể thao thoáng khí, phù hợp tập gym.', 'Áo tập Puma', 450000.00, 390000.00, 'PUMA-TS01', 2, 3, 'puma_tshirt.jpg', 200, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(5, 'Under Armour HeatGear Shirt', 'Áo thể thao công nghệ HeatGear giúp thoáng mát.', 'Áo UA thể thao', 600000.00, 12000000.00, 'UA-HG01', 2, 4, 'ua_heatgear.jpg', 150, 0, 'active', '2026-04-21 09:42:14', '2026-04-21 20:16:10'),
(6, 'Nike Dri-FIT Pants', 'Quần thể thao co giãn, thấm hút mồ hôi tốt.', 'Quần thể thao Nike', 800000.00, 720000.00, 'NIKE-PANTS01', 3, 1, 'nike_drifit_pants.jpg', 130, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(7, 'Adidas Track Pants', 'Quần track pants phong cách thể thao năng động.', 'Quần Adidas', 750000.00, 12000000.00, 'ADI-PANTS01', 3, 2, 'adidas_track_pants.jpg', 110, 0, 'active', '2026-04-21 09:42:14', '2026-04-21 20:16:44'),
(8, 'New Balance Running Shorts', 'Quần short chạy bộ nhẹ, thoáng khí.', 'Quần short NB', 500000.00, 450000.00, 'NB-SHORT01', 3, 5, 'nb_shorts.jpg', 90, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(9, 'Lululemon Yoga Pants', 'Quần yoga cao cấp, co giãn 4 chiều.', 'Quần yoga nữ', 1800000.00, 1600000.00, 'LULU-YOGA01', 3, 6, 'lululemon_yoga.jpg', 75, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(10, 'Nike Basic T-Shirt', 'Áo thun Nike chất liệu cotton thoáng mát', 'Áo Nike basic', 500000.00, 450000.00, 'NIKE-TSHIRT', 2, 1, 'nike_tshirt.jpg', 50, 1, 'active', '2026-04-21 10:09:13', '2026-04-21 10:09:13'),
(11, 'Adidas Running Shoes', 'Giày chạy bộ Adidas nhẹ và êm', 'Giày Adidas', 2000000.00, 1800000.00, 'ADI-SHOES', 1, 2, 'adidas_shoes.jpg', 30, 1, 'active', '2026-04-21 10:09:13', '2026-04-21 10:09:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL COMMENT 'Ảnh phụ, có thể có nhiều ảnh '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL COMMENT 'ID biến thể',
  `product_id` int(11) NOT NULL COMMENT 'Sản phẩm cha',
  `sku` varchar(100) DEFAULT NULL COMMENT 'Mã biến thể (quan trọng)',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Giá riêng (nếu khác product)',
  `image` varchar(255) DEFAULT NULL COMMENT 'Ảnh riêng của biến thể (màu đỏ, màu đen...)',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Các biến thể của sản phẩm (size, màu,...)';

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `sku`, `price`, `image`, `created_at`) VALUES
(1, 1, 'NIKE-TS-M-RED', 450000.00, 'nike_tshirt_red.jpg', '2026-04-21 10:09:18'),
(2, 1, 'NIKE-TS-M-BLACK', 450000.00, 'nike_tshirt_black.jpg', '2026-04-21 10:09:18'),
(3, 1, 'NIKE-TS-L-RED', 450000.00, 'nike_tshirt_red.jpg', '2026-04-21 10:09:18'),
(4, 1, 'NIKE-TS-L-BLACK', 450000.00, 'nike_tshirt_black.jpg', '2026-04-21 10:09:18'),
(5, 2, 'ADI-SHOES-40', 1800000.00, 'adidas_40.jpg', '2026-04-21 10:09:47'),
(6, 2, 'ADI-SHOES-41', 1800000.00, 'adidas_41.jpg', '2026-04-21 10:09:47'),
(7, 2, 'ADI-SHOES-42', 1800000.00, 'adidas_42.jpg', '2026-04-21 10:09:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff','admin') DEFAULT 'customer',
  `gender` enum('0','1','2') DEFAULT NULL,
  `number_phone` int(11) DEFAULT NULL,
  `gmail` varchar(255) DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role`, `gender`, `number_phone`, `gmail`, `create_at`) VALUES
(3, 'tinhvan', 'admin@gmail.com', '$2b$10$kTNqQGjejhMD6rnZ2ZJx/.m33PPAcTgXy6szqdQe6fWn4FVDysG3.', 'admin', '1', 818177533, 'tinhlu703@gmail.com', '2026-04-19 20:06:15'),
(4, 'Tính Văn ', 'tinhlu703@gmail.com', '$2b$10$Q.o5HJ4sMfrW5Qt1rQnSVuBdyoc8OjQxbEhWm91qIVV2tGlN3fODu', 'customer', '1', NULL, '', '2026-04-24 07:40:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_address`
--

CREATE TABLE `user_address` (
  `user_address_id` int(11) NOT NULL COMMENT 'ID địa chỉ',
  `user_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `recipient_name` varchar(255) NOT NULL COMMENT 'Tên người nhận',
  `recipient_phone` varchar(20) NOT NULL COMMENT 'Số điện thoại',
  `province_name` varchar(100) NOT NULL COMMENT 'Tỉnh / Thành phố',
  `district_name` varchar(100) NOT NULL COMMENT 'Quận / Huyện',
  `ward_name` varchar(100) NOT NULL COMMENT 'Phường / Xã',
  `specific_address` varchar(255) NOT NULL COMMENT 'Số nhà, đường, khu dân cư...',
  `is_default` tinyint(1) DEFAULT 0 COMMENT 'Địa chỉ mặc định',
  `label` varchar(50) DEFAULT NULL COMMENT 'Nhà riêng/công ty',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh sách địa chỉ của user, dùng để chọn khi đặt hàng (không dùng trực tiếp cho orders)';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variant_attributes`
--

CREATE TABLE `variant_attributes` (
  `id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `attribute_name` varchar(50) DEFAULT NULL COMMENT 'size, color',
  `attribute_value` varchar(50) DEFAULT NULL COMMENT 'M, red'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variant_attributes`
--

INSERT INTO `variant_attributes` (`id`, `variant_id`, `attribute_name`, `attribute_value`) VALUES
(1, 1, 'size', 'M'),
(2, 1, 'color', 'red'),
(3, 2, 'size', 'M'),
(4, 2, 'color', 'black'),
(5, 3, 'size', 'L'),
(6, 3, 'color', 'red'),
(7, 4, 'size', 'L'),
(8, 4, 'color', 'black'),
(9, 5, 'size', '40'),
(10, 6, 'size', '41'),
(11, 7, 'size', '42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL COMMENT 'Giảm tối đa bao nhiêu, vi du giam 50% toi da 100k',
  `min_order_value` decimal(10,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL COMMENT 'tong so luong dung',
  `used_count` int(11) DEFAULT 0 COMMENT 'da dung bao nhieu',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher_categories`
--

CREATE TABLE `voucher_categories` (
  `voucher_categorie_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher_products`
--

CREATE TABLE `voucher_products` (
  `voucher_product_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_variant` (`variant_id`);

--
-- Chỉ mục cho bảng `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`manufacturer_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`username`);

--
-- Chỉ mục cho bảng `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`user_address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `variant_attributes`
--
ALTER TABLE `variant_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `voucher_categories`
--
ALTER TABLE `voucher_categories`
  ADD PRIMARY KEY (`voucher_categorie_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `voucher_products`
--
ALTER TABLE `voucher_products`
  ADD PRIMARY KEY (`voucher_product_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID đơn hàng';

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID chi tiết đơn hàng';

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID biến thể', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `user_address`
--
ALTER TABLE `user_address`
  MODIFY `user_address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID địa chỉ';

--
-- AUTO_INCREMENT cho bảng `variant_attributes`
--
ALTER TABLE `variant_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `voucher_categories`
--
ALTER TABLE `voucher_categories`
  MODIFY `voucher_categorie_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `voucher_products`
--
ALTER TABLE `voucher_products`
  MODIFY `voucher_product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`),
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`manufacturer_id`);

--
-- Ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ràng buộc cho bảng `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `user_address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `variant_attributes`
--
ALTER TABLE `variant_attributes`
  ADD CONSTRAINT `variant_attributes_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `voucher_categories`
--
ALTER TABLE `voucher_categories`
  ADD CONSTRAINT `voucher_categories_ibfk_1` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`),
  ADD CONSTRAINT `voucher_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Ràng buộc cho bảng `voucher_products`
--
ALTER TABLE `voucher_products`
  ADD CONSTRAINT `voucher_products_ibfk_1` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`),
  ADD CONSTRAINT `voucher_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
