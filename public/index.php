<?php
// public/index.php
// Nạp file autoload của Composer
require_once '../vendor/autoload.php';

use Tinhl\Bai01QuanlySv\Controllers\StudentController;
// Simple Router
$action = $_GET['action'] ?? 'index';
$controller = new StudentController();
switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'index':
    default:
        $controller->index();
        break;
}
