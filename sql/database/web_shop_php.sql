-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 27, 2026 lúc 08:54 AM
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
  `add_at` timestamp NULL DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `product_id`, `add_at`, `quantity`) VALUES
(1, 3, 2, '2026-04-22 18:00:41', 1),
(30, 12, 12, '2026-04-27 05:47:45', 2),
(31, 12, 1, '2026-04-27 06:42:20', 1);

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
(3, 'pants', '2026-04-21 09:33:46', NULL),
(4, 'bags', '2026-04-25 12:54:35', NULL),
(5, 'Gi├áy B├│ng Rß╗ò', '2026-04-25 12:54:35', NULL),
(6, '├üo Kho├íc Thß╗â Thao', '2026-04-25 12:54:35', NULL),
(7, 'Quß║ºn Tß║¡p Gym', '2026-04-25 12:54:35', NULL),
(8, 'Phß╗Ñ Kiß╗çn', '2026-04-25 12:54:35', NULL),
(9, 'Giày chạy bộ', '2026-04-25 14:32:23', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favority`
--

CREATE TABLE `favority` (
  `farority_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 'Lululemon', '2026-04-21 09:35:55'),
(7, 'Chanel', '2026-04-25 12:54:35'),
(8, 'Adidas', '2026-04-25 12:54:35'),
(9, 'Puma', '2026-04-25 12:54:35'),
(10, 'New Balance', '2026-04-25 12:54:35'),
(11, 'Under Armour', '2026-04-25 12:54:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL COMMENT 'ID ????n h??ng',
  `user_id` int(11) NOT NULL COMMENT 'ID ng?????i d??ng ?????t h??ng',
  `order_code` varchar(50) DEFAULT NULL COMMENT 'M?? ????n h??ng hi???n th??? cho user (VD: ORD20260420001)',
  `status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending' COMMENT 'Tr???ng th??i ????n h??ng: pending(ch???), confirmed(x??c nh???n), shipping(??ang giao), completed(ho??n th??nh), cancelled(???? h???y)',
  `payment_status` enum('unpaid','paid','failed','refunded') DEFAULT 'unpaid' COMMENT 'Tr???ng th??i thanh to??n: unpaid(ch??a thanh to??n), paid(???? thanh to??n), failed(th???t b???i), refunded(???? ho??n ti???n)',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'T???ng ti???n s???n ph???m tr?????c khi gi???m gi?? v?? ph?? ship',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'S??? ti???n ???????c gi???m t??? voucher ho???c khuy???n m??i',
  `shipping_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'Ph?? v???n chuy???n',
  `total_amount` decimal(15,2) NOT NULL COMMENT 'T???ng ti???n cu???i c??ng kh??ch ph???i tr??? = subtotal - discount + shipping_fee',
  `voucher_id` int(11) DEFAULT NULL COMMENT 'ID voucher ???? s??? d???ng (c?? th??? NULL n???u kh??ng d??ng)',
  `voucher_code` varchar(50) DEFAULT NULL COMMENT 'Snapshot m?? voucher t???i th???i ??i???m ?????t h??ng',
  `voucher_discount` decimal(10,2) DEFAULT NULL COMMENT 'Snapshot s??? ti???n gi???m t??? voucher',
  `recipient_name` varchar(255) NOT NULL COMMENT 'T??n ng?????i nh???n (snapshot, kh??ng ph??? thu???c user_address)',
  `recipient_phone` varchar(20) NOT NULL COMMENT 'S??? ??i???n tho???i ng?????i nh???n',
  `province_name` varchar(100) NOT NULL COMMENT 'T??n t???nh/th??nh (snapshot)',
  `district_name` varchar(100) NOT NULL COMMENT 'T??n qu???n/huy???n (snapshot)',
  `ward_name` varchar(100) NOT NULL COMMENT 'T??n ph?????ng/x?? (snapshot)',
  `specific_address` varchar(255) NOT NULL COMMENT '?????a ch??? c??? th???: s??? nh??, ???????ng, khu d??n c??...',
  `user_address_id` int(11) DEFAULT NULL COMMENT 'ID ?????a ch??? c???a user (ch??? d??ng tham chi???u/g???i ??, kh??ng d??ng hi???n th???)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Th???i ??i???m t???o ????n h??ng',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Th???i ??i???m c???p nh???t g???n nh???t (tr???ng th??i, thanh to??n,...)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='B???ng l??u th??ng tin ????n h??ng: ng?????i mua, ?????a ch??? giao, tr???ng th??i, thanh to??n v?? t???ng ti???n (???? snapshot ????? ?????m b???o t??nh to??n v???n d??? li???u)';

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_code`, `status`, `payment_status`, `subtotal`, `discount_amount`, `shipping_fee`, `total_amount`, `voucher_id`, `voucher_code`, `voucher_discount`, `recipient_name`, `recipient_phone`, `province_name`, `district_name`, `ward_name`, `specific_address`, `user_address_id`, `created_at`, `updated_at`) VALUES
(1, 10, 'ORD00001', 'pending', 'paid', 4034129.00, 0.00, 0.00, 4034129.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(2, 4, 'ORD00002', 'completed', 'paid', 3962422.00, 0.00, 0.00, 3962422.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(3, 9, 'ORD00003', 'pending', 'paid', 3251055.00, 0.00, 0.00, 3251055.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(4, 4, 'ORD00004', 'confirmed', 'paid', 3675888.00, 0.00, 0.00, 3675888.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(5, 4, 'ORD00005', 'cancelled', 'paid', 5619185.00, 0.00, 0.00, 5619185.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(6, 4, 'ORD00006', 'completed', 'paid', 9649340.00, 0.00, 0.00, 9649340.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(7, 8, 'ORD00007', 'confirmed', 'paid', 2603900.00, 0.00, 0.00, 2603900.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(8, 9, 'ORD00008', 'shipping', 'paid', 8402612.00, 0.00, 0.00, 8402612.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(9, 10, 'ORD00009', 'completed', 'paid', 9150404.00, 0.00, 0.00, 9150404.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(10, 9, 'ORD00010', 'completed', 'paid', 1676544.00, 0.00, 0.00, 1676544.00, NULL, NULL, NULL, 'Ng╞░ß╗¥i nhß║¡n mß║½u', '0987654321', 'TP. Hß╗ô Ch├¡ Minh', 'Quß║¡n 1', 'Ph╞░ß╗¥ng Bß║┐n Ngh├⌐', '123 L├¬ Lß╗úi', NULL, '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(11, 14, 'ORD20260426133133712', 'pending', 'unpaid', 12300000.00, 0.00, 30000.00, 13560000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 11:31:33', '2026-04-26 11:31:33'),
(12, 14, 'ORD20260426133153414', 'pending', 'unpaid', 2900000.00, 0.00, 30000.00, 3220000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 11:31:53', '2026-04-26 11:31:53'),
(13, 14, 'ORD20260426133608224', 'pending', 'failed', 850000.00, 0.00, 30000.00, 965000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 11:36:08', '2026-04-26 11:47:38'),
(14, 14, 'ORD20260426134801518', 'pending', 'unpaid', 2900000.00, 0.00, 0.00, 3190000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '123', '123', '123', '123', NULL, '2026-04-26 11:48:01', '2026-04-26 11:48:01'),
(15, 14, 'ORD20260426134848653', 'pending', 'paid', 2800000.00, 0.00, 30000.00, 3110000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '123', '123', '123', '123', NULL, '2026-04-26 11:48:48', '2026-04-26 11:49:06'),
(16, 14, 'ORD20260426134947902', 'pending', 'unpaid', 4500000.00, 0.00, 30000.00, 4980000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 11:49:47', '2026-04-26 11:49:47'),
(17, 14, 'ORD20260426135418739', 'completed', 'paid', 3900000.00, 0.00, 30000.00, 4320000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 11:54:18', '2026-04-26 12:07:14'),
(18, 14, 'ORD20260426141414486', 'pending', 'unpaid', 2800000.00, 0.00, 30000.00, 3110000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 12:14:14', '2026-04-26 12:14:14'),
(19, 14, 'ORD20260426141640603', 'pending', 'paid', 2520000.00, 0.00, 50000.00, 2822000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 12:16:40', '2026-04-26 12:18:30'),
(20, 14, 'ORD20260426143014639', 'pending', 'unpaid', 2900000.00, 0.00, 0.00, 3190000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', 'Cần Thơ', 'Quận Ninh Kiều', 'Phường An Nghiệp', '', NULL, '2026-04-26 12:30:14', '2026-04-26 12:30:14'),
(21, 14, 'ORD20260426145724534', 'pending', 'unpaid', 2800000.00, 0.00, 30000.00, 3110000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 12:57:24', '2026-04-26 12:57:24'),
(22, 14, 'ORD20260426152318926', 'pending', 'unpaid', 3900000.00, 0.00, 30000.00, 4320000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', 'Hà Nội', 'Quận Ba Đình', 'Phường Trúc Bạch', '', NULL, '2026-04-26 13:23:18', '2026-04-26 13:23:18'),
(23, 14, 'ORD20260426152340380', 'pending', 'paid', 2900000.00, 0.00, 30000.00, 3220000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 13:23:40', '2026-04-26 13:24:19'),
(24, 14, 'ORD20260426152523275', 'pending', 'paid', 8700000.00, 0.00, 30000.00, 9600000.00, NULL, NULL, NULL, 'tathainguyen', '913775566', '', '', '', '', NULL, '2026-04-26 13:25:23', '2026-04-26 13:25:57'),
(25, 12, 'ORD20260426153549555', 'pending', 'unpaid', 6700000.00, 0.00, 30000.00, 7400000.00, NULL, NULL, NULL, 'Tính Văn ', '0', 'Cà Mau', 'Phú Tân', 'Tân Hải', 'Ấp Đầu Sấu', NULL, '2026-04-26 13:35:49', '2026-04-26 13:35:49'),
(26, 12, 'ORD20260426153742976', 'pending', 'unpaid', 3900000.00, 0.00, 30000.00, 4320000.00, NULL, NULL, NULL, 'Tính Văn ', '0818177533', 'Cà Mau', 'Phú Tân', 'Tân Hải', 'Ấp Đầu Sấu', NULL, '2026-04-26 13:37:42', '2026-04-26 13:37:42'),
(27, 12, 'ORD20260426154211443', 'pending', 'unpaid', 6700000.00, 0.00, 30000.00, 7400000.00, NULL, NULL, NULL, 'Tính Văn ', '0818177533', 'Cà Mau', 'Phú Tân', 'Tân Hải', 'Ấp Đầu Sấu', NULL, '2026-04-26 13:42:11', '2026-04-26 13:42:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL COMMENT 'ID chi ti???t ????n h??ng',
  `order_id` int(11) NOT NULL COMMENT 'Li??n k???t ?????n ????n h??ng',
  `product_id` int(11) NOT NULL COMMENT 'ID s???n ph???m',
  `variant_id` int(11) DEFAULT NULL COMMENT 'Bi???n th??? (size, m??u)',
  `product_name` varchar(255) NOT NULL COMMENT 'T??n s???n ph???m t???i th???i ??i???m mua',
  `product_image` varchar(255) DEFAULT NULL COMMENT '???nh s???n ph???m snapshot',
  `sku` varchar(100) DEFAULT NULL COMMENT 'M?? bi???n th???',
  `price` decimal(15,2) NOT NULL COMMENT 'Gi?? t???i th???i ??i???m mua',
  `quantity` int(11) NOT NULL COMMENT 'S??? l?????ng',
  `total_price` decimal(15,2) NOT NULL COMMENT 'price * quantity'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `variant_id`, `product_name`, `product_image`, `sku`, `price`, `quantity`, `total_price`) VALUES
(1, 1, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(2, 1, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(3, 2, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(4, 2, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(5, 3, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(6, 3, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(7, 4, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(8, 4, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(9, 5, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(10, 5, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(11, 6, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(12, 6, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(13, 7, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(14, 7, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(15, 8, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(16, 8, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(17, 9, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(18, 9, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(19, 10, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(20, 10, 1, NULL, 'Sß║ún phß║⌐m mß║½u', NULL, NULL, 500000.00, 1, 500000.00),
(21, 11, 15, NULL, 'Nike Jordan 1 Retro', 'nike_pegasus_40.jpg', NULL, 4500000.00, 1, 4500000.00),
(22, 11, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 2, 7800000.00),
(23, 12, 14, NULL, 'Puma Velocity Nitro', 'puma_tshirt.jpg', NULL, 2900000.00, 1, 2900000.00),
(24, 13, 17, NULL, 'Nike Dri-FIT Shorts', 'nike_drifit_pants.jpg', NULL, 850000.00, 1, 850000.00),
(25, 14, 2, NULL, 'Nike Air Zoom Pegasus 40', 'nike_pegasus_40.jpg', NULL, 2900000.00, 1, 2900000.00),
(26, 15, 12, NULL, 'Nike Air Zoom Pegasus', 'nike_pegasus_40.jpg', NULL, 2800000.00, 1, 2800000.00),
(27, 16, 15, NULL, 'Nike Jordan 1 Retro', 'nike_pegasus_40.jpg', NULL, 4500000.00, 1, 4500000.00),
(28, 17, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 1, 3900000.00),
(29, 18, 12, NULL, 'Nike Air Zoom Pegasus', 'nike_pegasus_40.jpg', NULL, 2800000.00, 1, 2800000.00),
(30, 19, 18, NULL, 'NB Classic 574', 'nb_shorts.jpg', NULL, 1800000.00, 1, 1800000.00),
(31, 19, 6, NULL, 'Nike Dri-FIT Pants', 'nike_drifit_pants.jpg', NULL, 720000.00, 1, 720000.00),
(32, 20, 14, NULL, 'Puma Velocity Nitro', 'puma_tshirt.jpg', NULL, 2900000.00, 1, 2900000.00),
(33, 21, 12, NULL, 'Nike Air Zoom Pegasus', 'nike_pegasus_40.jpg', NULL, 2800000.00, 1, 2800000.00),
(34, 22, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 1, 3900000.00),
(35, 23, 14, NULL, 'Puma Velocity Nitro', 'puma_tshirt.jpg', NULL, 2900000.00, 1, 2900000.00),
(36, 24, 14, NULL, 'Puma Velocity Nitro', 'puma_tshirt.jpg', NULL, 2900000.00, 3, 8700000.00),
(37, 25, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 1, 3900000.00),
(38, 25, 12, NULL, 'Nike Air Zoom Pegasus', 'nike_pegasus_40.jpg', NULL, 2800000.00, 1, 2800000.00),
(39, 26, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 1, 3900000.00),
(40, 27, 12, NULL, 'Nike Air Zoom Pegasus', 'nike_pegasus_40.jpg', NULL, 2800000.00, 1, 2800000.00),
(41, 27, 13, NULL, 'Adidas Ultraboost 22', 'adidas_ultraboost_22.jpg', NULL, 3900000.00, 1, 3900000.00);

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

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `method`, `status`, `transaction_code`, `paid_at`) VALUES
(1, 13, '965000.00', 'paypal', 'success', '9BY47744W4245320P', '2026-04-26 11:39:32'),
(2, 15, '3110000.00', 'paypal', 'success', '7A42076429312794U', '2026-04-26 11:49:06'),
(3, 17, '4320000.00', 'vnpay', 'success', '15514572', '2026-04-26 11:55:57'),
(4, 19, '2822000.00', 'vnpay', 'success', '15514578', '2026-04-26 12:18:30'),
(5, 23, '3220000.00', 'vnpay', 'success', '15514605', '2026-04-26 13:24:19'),
(6, 24, '9600000.00', 'paypal', 'success', '99C981735L7693028', '2026-04-26 13:25:57');

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
(1, ' Pickleball NikeCourt Air Zoom Vapor 11', NULL, 'Gi???y th??? thao cho nam', 2950000.00, 1500000.00, '123425', 1, NULL, 'nikecourt-air-zoom-vapor-11-mens-hard-court-tennis-shoes-03_720x720xcrop-preview.png', 0, 1, 'active', '2026-04-21 03:31:35', '2026-04-23 01:25:12'),
(2, 'Nike Air Zoom Pegasus 40', 'Gi??y ch???y b??? cao c???p v???i c??ng ngh??? Zoom Air, ph?? h???p cho luy???n t???p v?? thi ?????u.', 'Gi??y ch???y b??? Nike', 3200000.00, 2900000.00, 'NIKE-PEG40', 1, 1, 'nike_pegasus_40.jpg', 120, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(3, 'Adidas Ultraboost 22', 'Gi??y ch???y b??? v???i ????? Boost ??m ??i, ho??n tr??? n??ng l?????ng t???t.', 'Gi??y ch???y Adidas', 3500000.00, 3100000.00, 'ADI-UB22', 1, 2, 'adidas_ultraboost_22.jpg', 95, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(4, 'Puma Training T-Shirt', '??o thun th??? thao tho??ng kh??, ph?? h???p t???p gym.', '??o t???p Puma', 450000.00, 390000.00, 'PUMA-TS01', 2, 3, 'puma_tshirt.jpg', 200, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(5, 'Under Armour HeatGear Shirt', '??o th??? thao c??ng ngh??? HeatGear gi??p tho??ng m??t.', '??o UA th??? thao', 600000.00, 12000000.00, 'UA-HG01', 2, 4, 'ua_heatgear.jpg', 150, 0, 'active', '2026-04-21 09:42:14', '2026-04-21 20:16:10'),
(6, 'Nike Dri-FIT Pants', '', '', 800000.00, 720000.00, 'NIKE-PANTS01', 2, 1, 'nike_drifit_pants.jpg', 130, 1, 'active', '2026-04-21 09:42:14', '2026-04-27 04:47:01'),
(7, 'Adidas Track Pants', 'Qu???n track pants phong c??ch th??? thao n??ng ?????ng.', 'Qu???n Adidas', 750000.00, 12000000.00, 'ADI-PANTS01', 3, 2, 'adidas_track_pants.jpg', 110, 0, 'active', '2026-04-21 09:42:14', '2026-04-21 20:16:44'),
(8, 'New Balance Running Shorts', 'Qu???n short ch???y b??? nh???, tho??ng kh??.', 'Qu???n short NB', 500000.00, 450000.00, 'NB-SHORT01', 3, 5, 'nb_shorts.jpg', 90, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(9, 'Lululemon Yoga Pants', 'Qu???n yoga cao c???p, co gi??n 4 chi???u.', 'Qu???n yoga n???', 1800000.00, 1600000.00, 'LULU-YOGA01', 3, 6, 'lululemon_yoga.jpg', 75, 1, 'active', '2026-04-21 09:42:14', '2026-04-21 09:42:14'),
(10, 'Nike Basic T-Shirt', '??o thun Nike ch???t li???u cotton tho??ng m??t', '??o Nike basic', 500000.00, 450000.00, 'NIKE-TSHIRT', 2, 1, 'nike_tshirt.jpg', 50, 1, 'active', '2026-04-21 10:09:13', '2026-04-21 10:09:13'),
(11, 'Adidas Running Shoes', 'Gi??y ch???y b??? Adidas nh??? v?? ??m', 'Gi??y Adidas', 2000000.00, 1800000.00, 'ADI-SHOES', 1, 2, 'adidas_shoes.jpg', 30, 1, 'active', '2026-04-21 10:09:13', '2026-04-21 10:09:13'),
(12, 'Nike Air Zoom Pegasus', '', NULL, 3500000.00, 2800000.00, 'NIKE-PEG-01', 1, 9, 'nike_pegasus_40.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 14:33:39'),
(13, 'Adidas Ultraboost 22', '', NULL, 4500000.00, 3900000.00, 'ADI-UB-22', 1, 4, 'adidas_ultraboost_22.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 14:33:45'),
(14, 'Puma Velocity Nitro', NULL, NULL, 2900000.00, NULL, 'PUMA-VEL-01', 2, 6, 'nikecourt-air-zoom-vapor-11-mens-hard-court-tennis-shoes-03_720x720xcrop-preview.png', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-27 06:34:00'),
(15, 'Nike Jordan 1 Retro', '', NULL, 5000000.00, 4500000.00, 'NIKE-JD1-01', 1, 11, 'nike_pegasus_40.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 14:33:50'),
(16, 'Adidas Track Pants', '', NULL, 1200000.00, 950000.00, 'ADI-TP-01', 3, 3, 'adidas_track_pants.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 14:33:56'),
(17, 'Nike Dri-FIT Shorts', NULL, NULL, 850000.00, NULL, 'NIKE-DF-01', 1, 3, 'nike_drifit_pants.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(18, 'NB Classic 574', NULL, NULL, 2200000.00, 1800000.00, 'NB-574-01', 3, 7, 'nb_shorts.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 12:54:35'),
(19, 'Áo tập gym', 'Áo tập gym co giãn', NULL, 750000.00, 600000.00, 'UA-HG-01', 2, 2, 'ua_heatgear.jpg', 0, 1, 'active', '2026-04-25 12:54:35', '2026-04-25 14:33:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL COMMENT '???nh ph???, c?? th??? c?? nhi???u ???nh '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL COMMENT 'ID bi???n th???',
  `product_id` int(11) NOT NULL COMMENT 'S???n ph???m cha',
  `sku` varchar(100) DEFAULT NULL COMMENT 'M?? bi???n th??? (quan tr???ng)',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Gi?? ri??ng (n???u kh??c product)',
  `image` varchar(255) DEFAULT NULL COMMENT '???nh ri??ng c???a bi???n th??? (m??u ?????, m??u ??en...)',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='C??c bi???n th??? c???a s???n ph???m (size, m??u,...)';

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
  `status` enum('active','locked') DEFAULT 'active',
  `gender` enum('0','1','2') DEFAULT NULL,
  `number_phone` int(11) DEFAULT NULL,
  `gmail` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `create_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role`, `status`, `gender`, `number_phone`, `gmail`, `avatar`, `create_at`) VALUES
(3, 'tinhvan', '1', '1', 'admin', 'active', '1', 818177533, 'tinhlu703@gmail.com', 'default_avatar.png', '2026-04-19 20:06:15'),
(4, 'T├¡nh V─ân', 'tinhlu703@gmail.com', '$2b$10$Q.o5HJ4sMfrW5Qt1rQnSVuBdyoc8OjQxbEhWm91qIVV2tGlN3fODu', 'customer', 'active', '1', 0, '', 'default_avatar.png', '2026-04-24 07:40:36'),
(12, 'Tính Văn ', 'username12345', '$2y$10$YNZIEDYKYFeWhAN2InDlnOZVASzo.3I2XnGeHz4u7ia6wfTs0kxta', 'customer', 'active', '1', 0, 'tinhlu703@gmail.com', 'default_avatar.png', '2026-04-26 07:19:52'),
(13, 'Admin', 'admin', '$2y$10$uIlQiJFyJsCFbEanXxDeEeX7geY9vISXiyZ5xRpbF.uLBJadg9OEy', 'admin', 'active', '1', 0, '', 'default_avatar.png', '2026-04-26 07:20:32'),
(14, 'tathainguyen', 'thainguyen24', '$2y$10$Ijy7rD4l00LE6UkiKG9c0ueRuVnl1vDLuiOZTP2FKSRXDit.oaDIa', 'customer', 'active', '1', 913775566, 'tathainguyen686@gmail.com', 'default_avatar.png', '2026-04-26 08:56:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_address`
--

CREATE TABLE `user_address` (
  `user_address_id` int(11) NOT NULL COMMENT 'ID ?????a ch???',
  `user_id` int(11) NOT NULL COMMENT 'ID ng?????i d??ng',
  `recipient_name` varchar(255) NOT NULL COMMENT 'T??n ng?????i nh???n',
  `recipient_phone` varchar(20) NOT NULL COMMENT 'S??? ??i???n tho???i',
  `province_name` varchar(100) NOT NULL COMMENT 'T???nh / Th??nh ph???',
  `district_name` varchar(100) NOT NULL COMMENT 'Qu???n / Huy???n',
  `ward_name` varchar(100) NOT NULL COMMENT 'Ph?????ng / X??',
  `specific_address` varchar(255) NOT NULL COMMENT 'S??? nh??, ???????ng, khu d??n c??...',
  `is_default` tinyint(1) DEFAULT 0 COMMENT '?????a ch??? m???c ?????nh',
  `label` varchar(50) DEFAULT NULL COMMENT 'Nh?? ri??ng/c??ng ty',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Ng??y t???o',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ng??y c???p nh???t'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh s??ch ?????a ch??? c???a user, d??ng ????? ch???n khi ?????t h??ng (kh??ng d??ng tr???c ti???p cho orders)';

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
  `max_discount` decimal(10,2) DEFAULT NULL COMMENT 'Gi???m t???i ??a bao nhi??u, vi du giam 50% toi da 100k',
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
-- Chỉ mục cho bảng `favority`
--
ALTER TABLE `favority`
  ADD PRIMARY KEY (`farority_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `favority`
--
ALTER TABLE `favority`
  MODIFY `farority_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID ????n h??ng', AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID chi ti???t ????n h??ng', AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID bi???n th???', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `user_address`
--
ALTER TABLE `user_address`
  MODIFY `user_address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID ?????a ch???';

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
-- Ràng buộc cho bảng `favority`
--
ALTER TABLE `favority`
  ADD CONSTRAINT `favority_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `favority_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
