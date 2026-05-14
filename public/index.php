<?php

header('Content-Type: text/html; charset=utf-8');
session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once PROJECT_ROOT . '/app/controller.php';

// Load Admin Controllers
require_once PROJECT_ROOT . '/app/Admin/DashboardController.php';
require_once PROJECT_ROOT . '/app/Admin/UserController.php';
require_once PROJECT_ROOT . '/app/Admin/ProductController.php';
require_once PROJECT_ROOT . '/app/Admin/OrderController.php';
require_once PROJECT_ROOT . '/app/Admin/CustomerController.php';
require_once PROJECT_ROOT . '/app/Admin/BannerController.php';
require_once PROJECT_ROOT . '/app/Admin/VoucherController.php';
require_once PROJECT_ROOT . '/app/Admin/ReportController.php';
require_once PROJECT_ROOT . '/app/Admin/BlogAdminController.php';
require_once PROJECT_ROOT . '/app/CartController.php';
require_once PROJECT_ROOT . '/app/CheckoutController.php';
require_once PROJECT_ROOT . '/app/PaymentController.php';
require_once PROJECT_ROOT . '/app/BlogController.php';
require_once PROJECT_ROOT . '/app/PageController.php';
$action = $_GET['action'] ?? 'index';
$controller = new Controller();

// Admin Controller Instances
$dashboardCtrl = new DashboardController();
$userCtrl = new UserController();
$productCtrl = new ProductController();
$orderCtrl = new OrderController();
$customerCtrl = new CustomerController();
$bannerCtrl = new BannerController();
$voucherCtrl = new VoucherController();
$cartCtrl = new CartController();
$checkoutCtrl = new CheckoutController();
$paymentCtrl = new PaymentController();
$blogCtrl = new BlogController();
$pageCtrl = new PageController();
$blogAdminCtrl = new BlogAdminController();
$reportCtrl = new ReportController();
switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'category':
        $controller->category();
        break;
    case 'cart':
        $cartCtrl->index();
        break;
    case 'add_to_cart':
        $cartCtrl->add();
        break;
    case 'update_cart':
        $cartCtrl->update();
        break;
    case 'remove_from_cart':
        $cartCtrl->remove();
        break;

    // --- Checkout & Payment Routes ---
    case 'checkout':
        $checkoutCtrl->index();
        break;
    case 'process_checkout':
        $checkoutCtrl->process();
        break;
    case 'apply_voucher':
        $checkoutCtrl->applyVoucher();
        break;
    case 'checkout_success':
        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/checkout_success.php';
        include_once PROJECT_ROOT . '/components/footer.php';
        break;
    case 'checkout_failed':
        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/checkout_failed.php';
        include_once PROJECT_ROOT . '/components/footer.php';
        break;
    case 'vnpay_return':
        $paymentCtrl->vnpayReturn();
        break;
    case 'paypal_return':
        $paymentCtrl->paypalReturn();
        break;
    case 'login':
        $controller->login();
        break;
    case 'register':
        $controller->register();
        break;
    case 'handleRegister':
        $controller->handleRegister();
        break;
    case 'handleLogin':
        $controller->handleLogin();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'toggle_favorite':
        $controller->toggleFavorite();
        break;
    case 'profile':
        $controller->getProfileByUser();
        break;
    case 'edit_profile':
        $controller->editProfile();
        break;
    case 'update_profile':
        $controller->updateProfile();
        break;
    case 'search':
        $controller->search();
        break;
    case 'add_review':
        $controller->addReview();
        break;
    case 'get_reviews':
        $controller->getReviews();
        break;
    case 'best_sellers':
        $controller->bestSellers();
        break;
    case 'products':
        $controller->products();
        break;
    case 'orderDetail':
        $controller->orderDetail();
        break;


    // --- BLOG ROUTES ---
    case 'blogs':
        $blogCtrl->index();
        break;
    case 'blog_detail':
        $blogCtrl->detail();
        break;
    case 'blog_category':
        $blogCtrl->filterByCategory();
        break;
    case 'blog_search':
        $blogCtrl->search();
        break;

    // --- ADMIN ROUTES ---

    // Dashboard
    case 'admin_dashboard':
        $dashboardCtrl->index();
        break;

    // Users
    case 'admin_users':
        $userCtrl->list();
        break;
    case 'user_form':
        $userCtrl->form();
        break;
    case 'save_user':
        $userCtrl->save();
        break;
    case 'delete_user':
        $userCtrl->delete();
        break;
    case 'toggle_user_status':
        $userCtrl->toggleStatus();
        break;

    // Categories
    case 'admin_categories':
        $productCtrl->categories();
        break;
    case 'category_form':
        $productCtrl->categoryForm();
        break;
    case 'save_category':
        $productCtrl->saveCategory();
        break;
    case 'delete_category':
        $productCtrl->deleteCategory();
        break;

    // Manufacturers
    case 'admin_manufacturers':
        $productCtrl->manufacturers();
        break;
    case 'manufacturer_form':
        $productCtrl->manufacturerForm();
        break;
    case 'save_manufacturer':
        $productCtrl->saveManufacturer();
        break;
    case 'delete_manufacturer':
        $productCtrl->deleteManufacturer();
        break;

    // Products
    case 'admin_products':
        $productCtrl->list();
        break;
    case 'product_form':
        $productCtrl->form();
        break;
    case 'save_product':
        $productCtrl->save();
        break;
    case 'delete_product_image':
        $productCtrl->deleteImage();
        break;
    case 'delete_product':
        $productCtrl->delete();
        break;

    // Orders
    case 'admin_orders':
        $orderCtrl->list();
        break;
    case 'order_detail':
        $orderCtrl->detail();
        break;
    case 'update_order_status':
        $orderCtrl->updateStatus();
        break;

    // Customers
    case 'admin_customers':
        $customerCtrl->list();
        break;
    case 'admin_customer_detail':
        $customerCtrl->detail();
        break;

    // Banners
    case 'admin_banners':
        $bannerCtrl->list();
        break;
    case 'banner_form':
        $bannerCtrl->form();
        break;
    case 'save_banner':
        $bannerCtrl->save();
        break;
    case 'delete_banner':
        $bannerCtrl->delete();
        break;

    // Vouchers
    case 'admin_vouchers':
        $voucherCtrl->list();
        break;
    case 'voucher_form':
        $voucherCtrl->form();
        break;
    case 'save_voucher':
        $voucherCtrl->save();
        break;
    case 'delete_voucher':
        $voucherCtrl->delete();
        break;

    // Reports
    case 'revenue_report':
        $reportCtrl->revenueReport();
        break;
    case 'product_report':
        $reportCtrl->productReport();
        break;
    case 'order_report':
        $reportCtrl->orderReport();
        break;
    case 'customer_report':
        $reportCtrl->customerReport();
        break;

    // Blog Admin
    case 'admin_blogs':
        $blogAdminCtrl->list();
        break;
    case 'blog_admin_form':
        $blogAdminCtrl->form();
        break;
    case 'blog_save':
        $blogAdminCtrl->save();
        break;
    case 'blog_delete':
        $blogAdminCtrl->delete();
        break;
    case 'blog_change_status':
        $blogAdminCtrl->changeStatus();
        break;

    // Pages Admin
    case 'admin_pages':
        $pageCtrl->list();
        break;
    case 'page_admin_form':
        $pageCtrl->form();
        break;
    case 'page_save':
        $pageCtrl->save();
        break;
    case 'page_delete':
        $pageCtrl->delete();
        break;
    case 'page_view':
        $pageCtrl->view();
        break;

    default:
        $controller->index();
        break;
}
