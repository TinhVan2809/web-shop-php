<?php

session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';


switch ($action) {
    case 'index':
        $controller->index();
        break;
    default:
        $controller->index();
        break;
}
