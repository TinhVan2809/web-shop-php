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

$action = $_GET['action'] ?? 'index';
$controller = new Controller();

// Admin Controller Instances
$dashboardCtrl = new DashboardController();
$userCtrl = new UserController();
$productCtrl = new ProductController();
$orderCtrl = new OrderController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'login':
        $controller->login();
        break;
    case 'handleLogin':
        $controller->handleLogin();
        break;
    case 'logout':
        $controller->logout();
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

    default:
        $controller->index();
        break;
}
