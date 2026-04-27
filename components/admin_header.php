<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Haseki Store</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background-color: #1a1a1a; color: white; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center">
                        <i class="ri-flashlight-fill text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">Haseki Admin</span>
                </div>

                <nav class="space-y-1">
                    <a href="index.php?action=admin_dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= ($_GET['action'] == 'admin_dashboard') ? 'active' : '' ?>">
                        <i class="ri-dashboard-line"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="index.php?action=admin_users" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= ($_GET['action'] == 'admin_users') ? 'active' : '' ?>">
                        <i class="ri-user-line"></i>
                        <span class="font-medium">Người dùng</span>
                    </a>
                    <a href="index.php?action=admin_categories" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= ($_GET['action'] == 'admin_categories') ? 'active' : '' ?>">
                        <i class="ri-folder-line"></i>
                        <span class="font-medium">Danh mục</span>
                    </a>
                    <a href="index.php?action=admin_manufacturers" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= ($_GET['action'] == 'admin_manufacturers') ? 'active' : '' ?>">
                        <i class="ri-medal-line"></i>
                        <span class="font-medium">Thương hiệu</span>
                    </a>
                    <a href="index.php?action=admin_products" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= ($_GET['action'] == 'admin_products') ? 'active' : '' ?>">
                        <i class="ri-box-3-line"></i>
                        <span class="font-medium">Sản phẩm</span>
                    </a>
                    <a href="index.php?action=admin_orders" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= (isset($_GET['action']) && $_GET['action'] == 'admin_orders') ? 'active' : '' ?>">
                        <i class="ri-shopping-cart-2-line"></i>
                        <span class="font-medium">Đơn hàng</span>
                    </a>
                    <a href="index.php?action=admin_customers" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors <?= (isset($_GET['action']) && in_array($_GET['action'], ['admin_customers','admin_customer_detail'])) ? 'active' : '' ?>">
                        <i class="ri-team-line"></i>
                        <span class="font-medium">Khách hàng</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
                <h1 class="text-lg font-semibold text-gray-800">
                    <?php
                    switch($_GET['action'] ?? '') {
                        case 'admin_dashboard':        echo 'Dashboard'; break;
                        case 'admin_users':            echo 'Quản lý người dùng'; break;
                        case 'admin_products':         echo 'Quản lý sản phẩm'; break;
                        case 'admin_orders':           echo 'Quản lý đơn hàng'; break;
                        case 'admin_customers':        echo 'Quản lý khách hàng'; break;
                        case 'admin_customer_detail':  echo 'Chi tiết khách hàng'; break;
                        default: echo 'Quản trị';
                    }
                    ?>
                </h1>
                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-full"><i class="ri-notification-3-line"></i></button>
                    <div class="h-8 w-[1px] bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">A</div>
                        <span class="text-sm font-medium">Administrator</span>
                    </div>
                </div>
            </header>

            <div class="p-8">
