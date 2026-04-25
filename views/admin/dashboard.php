<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-user-heart-line"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Tổng người dùng</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_users']) ?></h3>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-shopping-bag-3-line"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Sản phẩm</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_products']) ?></h3>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-bill-line"></i>
            </div>
            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full">-2%</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Đơn hàng mới</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_orders']) ?></h3>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-money-dollar-circle-line"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+18%</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Doanh thu</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_revenue'], 0, ',', '.') ?>₫</h3>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
    <h3 class="text-lg font-bold mb-6">Chào mừng quay trở lại, Admin!</h3>
    <p class="text-gray-600 leading-relaxed">
        Hệ thống quản trị Haseki Store đã được thiết lập. Bạn có thể sử dụng menu bên trái để quản lý người dùng, sản phẩm và theo dõi các đơn hàng mới nhất.
    </p>
    <div class="mt-8 flex gap-4">
        <a href="index.php?action=admin_products" class="bg-black text-white px-6 py-3 rounded-xl font-medium hover:bg-gray-800 transition-all">Thêm sản phẩm mới</a>
        <a href="index.php?action=admin_orders" class="border border-gray-200 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition-all">Xem đơn hàng</a>
    </div>
</div>
