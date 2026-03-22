<?php
session_start();
// public/index.php
// Nạp file autoload của Composer
define('PROJECT_ROOT', dirname(__DIR__));
// Nạp file autoload của Composer
use Tinhl\Bai01QuanlySv\Controllers\UserController;
require_once PROJECT_ROOT . '../vendor/autoload.php';

use Tinhl\Bai01QuanlySv\Controllers\StudentController;
// Simple Router
$action = $_GET['action'] ?? 'index';
// Danh sách các action không yêu cầu đăng nhập
$public_actions = ['login', 'register', 'do_login',
'do_register'];

if (!in_array($action, $public_actions) &&
!isset($_SESSION['user_id'])) {
header('Location: index.php?action=login');
exit();
}

if (in_array($action, [
    'login',
    'register',
    'do_login',
    'do_register',
    'logout'
])) {
    $controller = new UserController();
} else {
    $controller = new StudentController();
}
switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'login':
        $controller->showLoginForm();
        break;
    case 'do_login':
        $controller->login();
        break;
    case 'register':
        $controller->showRegisterForm();
        break;
    case 'do_register':
        $controller->register();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->index();
        break;
}