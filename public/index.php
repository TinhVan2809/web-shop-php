<?php

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
require_once PROJECT_ROOT . '/app/CartController.php';
require_once PROJECT_ROOT . '/app/CheckoutController.php';
require_once PROJECT_ROOT . '/app/PaymentController.php';
$action = $_GET['action'] ?? 'index';
$controller = new Controller();

// Admin Controller Instances
$dashboardCtrl = new DashboardController();
$userCtrl = new UserController();
$productCtrl = new ProductController();
$orderCtrl = new OrderController();
$customerCtrl = new CustomerController();
$cartCtrl = new CartController();
$checkoutCtrl = new CheckoutController();
$paymentCtrl = new PaymentController();
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

    default:
        $controller->index();
        break;
}
