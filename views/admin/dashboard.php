<?php

/**
 * @var array $stats Dashboard statistics (total_users, total_products, total_orders, total_revenue)
 * @var array $chartLabels Revenue chart labels for last 7 days
 * @var array $chartValues Revenue chart values for last 7 days
 * @var array $lowStockProducts Low stock products
 */
?>
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
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-archive-line"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 font-medium">Tổng tồn kho</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_stock']) ?></h3>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl">
                <i class="ri-error-warning-3-line"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 font-medium">Hết hàng</p>
        <h3 class="text-2xl font-bold mt-1 text-rose-600"><?= number_format($stats['out_of_stock']) ?></h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Biểu đồ doanh thu -->
    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold mb-6">Xu hướng doanh thu (7 ngày qua)</h3>
        <div class="h-[350px] w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ sản phẩm bán chạy -->
    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold mb-6">Top 10 sản phẩm bán chạy (7 ngày qua)</h3>
        <div class="h-[350px] w-full">
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Biểu đồ phân bổ tồn kho -->
    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold mb-6">Tình trạng kho hàng</h3>
        <div class="h-[350px] w-full flex items-center justify-center">
            <canvas id="stockStatusChart"></canvas>
        </div>
    </div>
    <!-- Biểu đồ tỷ lệ danh mục bán ra -->
<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
    <h3 class="text-lg font-bold mb-6">Tỷ lệ sản phẩm bán ra theo danh mục</h3>
    <div class="h-[350px] w-full flex items-center justify-center">
        <canvas id="categorySalesChart"></canvas>
    </div>
</div>

</div>




<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
        <p class="text-sm font-medium text-gray-500 uppercase">Sản phẩm sắp hết hàng</p>
        <h3 class="text-2xl font-bold mt-1 text-red-600"><?= count($lowStockProducts ?? []) ?> sản phẩm</h3>
    </div>
</div>

<!-- Bảng 5 đơn hàng mới nhất -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="ri-shopping-cart-2-line text-blue-500"></i> Đơn hàng mới nhất
        </h2>
        <a href="index.php?action=admin_orders" class="text-sm text-blue-600 hover:underline">Xem tất cả -></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4">Mã đơn</th>
                    <th class="px-6 py-4">Khách hàng</th>
                    <th class="px-6 py-4">Ngày đặt</th>
                    <th class="px-6 py-4">Tổng tiền</th>
                    <th class="px-6 py-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($latestOrders as $order): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">#<?= $order['order_code'] ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td class="px-6 py-4 text-sm font-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full uppercase 
                                <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bảng sản phẩm tồn kho thấp -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="ri-error-warning-line text-red-500"></i> Cảnh báo tồn kho thấp
        </h2>
        <a href="index.php?action=admin_products" class="text-sm text-blue-600 hover:underline">Quản lý kho -></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Hiện có</th>
                    <th class="px-6 py-4">Mức tối thiểu</th>
                    <th class="px-6 py-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($lowStockProducts)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lowStockProducts as $item): ?>
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="../asset/<?= $item['thumbnail'] ?>" class="w-10 h-10 rounded object-cover border">
                                <span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= $item['variant_sku'] ?: $item['product_sku'] ?></td>
                            <td class="px-6 py-4 font-bold text-red-600"><?= $item['available_quantity'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $item['min_stock_level'] ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">
                                    <?= $item['available_quantity'] == 0 ? 'Hết hàng' : 'Sắp hết' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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

<!-- Báo Cáo & Thống Kê Section -->
<div class="mt-12 border-t border-gray-200 pt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="ri-bar-chart-box-line text-blue-600"></i> Báo Cáo & Thống Kê
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Report -->
        <a href="index.php?action=revenue_report" class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6 hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <i class="ri-money-dollar-circle-line text-3xl text-blue-600"></i>
                <i class="ri-arrow-right-line text-xl text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h4 class="font-bold text-gray-900">Báo Cáo Doanh Thu</h4>
            <p class="text-sm text-gray-600 mt-1">Theo ngày, tháng, năm</p>
        </a>

        <!-- Product Report -->
        <a href="index.php?action=product_report" class="group bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6 hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <i class="ri-shopping-bag-line text-3xl text-green-600"></i>
                <i class="ri-arrow-right-line text-xl text-green-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h4 class="font-bold text-gray-900">Báo Cáo Sản Phẩm</h4>
            <p class="text-sm text-gray-600 mt-1">Bán chạy, tồn kho, doanh thu</p>
        </a>

        <!-- Order Report -->
        <a href="index.php?action=order_report" class="group bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm border border-orange-200 p-6 hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <i class="ri-shopping-cart-2-line text-3xl text-orange-600"></i>
                <i class="ri-arrow-right-line text-xl text-orange-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h4 class="font-bold text-gray-900">Báo Cáo Đơn Hàng</h4>
            <p class="text-sm text-gray-600 mt-1">Theo trạng thái & thanh toán</p>
        </a>

        <!-- Customer Report -->
        <a href="index.php?action=customer_report" class="group bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6 hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <i class="ri-user-star-line text-3xl text-purple-600"></i>
                <i class="ri-arrow-right-line text-xl text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h4 class="font-bold text-gray-900">Báo Cáo Khách Hàng</h4>
            <p class="text-sm text-gray-600 mt-1">Top buyers, loyal, at-risk</p>
        </a>
    </div>

    <!-- Blogs Management Link -->
    <div class="bg-gradient-to-r from-indigo-50 to-pink-50 rounded-xl shadow-sm border border-indigo-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="ri-article-line text-indigo-600"></i> Quản Lý Blogs
                </h3>
                <p class="text-sm text-gray-600 mt-2">Xem, chỉnh sửa và quản lý các bài viết blog trên website</p>
            </div>
            <a href="index.php?action=blogs" target="_blank" class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium whitespace-nowrap">
                <i class="ri-external-link-line"></i> Xem Blogs
            </a>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Dữ liệu từ PHP truyền sang
        const labels = <?= json_encode($chartLabels) ?>;
        const dataValues = <?= json_encode($chartValues) ?>;
        const topProductLabels = <?= json_encode($topProductLabels) ?>;
        const topProductValues = <?= json_encode($topProductValues) ?>;
        const categoryChartLabels = <?= json_encode($categoryChartLabels) ?>;
        const categoryChartValues = <?= json_encode($categoryChartValues) ?>;
        const stockStatus = <?= json_encode($stockStatus) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: dataValues,
                    borderColor: '#2563eb', // Blue-600
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ sản phẩm bán chạy
        const ctx2 = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: topProductLabels,
                datasets: [{
                    label: 'Số lượng bán ra',
                    data: topProductValues,
                    backgroundColor: '#f97316', // Orange-500
                    borderRadius: 8,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y', // Chuyển thành biểu đồ ngang để dễ đọc tên sản phẩm dài
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ Doughnut trạng thái kho hàng
        const ctxStock = document.getElementById('stockStatusChart').getContext('2d');
        new Chart(ctxStock, {
            type: 'doughnut',
            data: {
                labels: ['Còn hàng', 'Sắp hết', 'Hết hàng'],
                datasets: [{
                    data: [stockStatus.in_stock, stockStatus.low_stock, stockStatus.out_of_stock],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ Doughnut tỷ lệ danh mục bán ra
        const ctxCategory = document.getElementById('categorySalesChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: categoryChartLabels,
                datasets: [{
                    data: categoryChartValues,
                    backgroundColor: [
                        '#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0',
                        '#00BCD4', '#8BC34A', '#FFEB3B', '#607D8B', '#E91E63'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    });
</script>