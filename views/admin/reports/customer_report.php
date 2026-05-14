<?php
$metric = $metric ?? 'top_buyers';
$customers = $customers ?? [];
$stats = $stats ?? [];
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-user-star-line text-purple-600"></i> Báo Cáo Khách Hàng
        </h1>
        <p class="text-gray-600 mt-2">Phân tích khách hàng tiềm năng, trung thành và có rủi ro</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Tổng Khách Hàng</p>
            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $stats['total_customers'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Khách Mới (30 Ngày)</p>
            <p class="text-3xl font-bold text-blue-600 mt-2"><?= $stats['new_customers_30d'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Doanh Thu Tất Cả Thời Gian</p>
            <p class="text-2xl font-bold text-green-600 mt-2">
                <?= number_format($stats['total_revenue_all_time'] ?? 0, 0, ',', '.') ?>₫
            </p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex gap-2 flex-wrap">
            <a href="index.php?action=customer_report&metric=top_buyers" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $metric === 'top_buyers' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                <i class="ri-trophy-line"></i> Top Buyers
            </a>
            <a href="index.php?action=customer_report&metric=loyal_customers" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $metric === 'loyal_customers' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                <i class="ri-heart-line"></i> Khách Loyal
            </a>
            <a href="index.php?action=customer_report&metric=at_risk" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $metric === 'at_risk' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:border-red-600' ?>">
                <i class="ri-alert-line"></i> Có Rủi Ro
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">
                <?php 
                if ($metric === 'top_buyers') echo 'Top Khách Hàng Chi Tiêu Cao';
                elseif ($metric === 'loyal_customers') echo 'Khách Hàng Trung Thành';
                else echo 'Khách Hàng Có Rủi Ro';
                ?>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">Khách Hàng</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Số Đơn</th>
                        <th class="px-6 py-4">Tổng Chi Tiêu</th>
                        <th class="px-6 py-4">Trung Bình/Đơn</th>
                        <th class="px-6 py-4">Ngày Cuối Cùng Mua</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($customer['name']) ?></p>
                                        <p class="text-xs text-gray-500">ID: <?= $customer['user_id'] ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 text-sm"><?= htmlspecialchars($customer['gmail']) ?></td>
                                <td class="px-6 py-4 text-center font-bold text-blue-600">
                                    <?= $customer['total_orders'] ?? 0 ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    <?= number_format($customer['total_spent'] ?? 0, 0, ',', '.') ?>₫
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?= number_format($customer['avg_order_value'] ?? 0, 0, ',', '.') ?>₫
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php if (!empty($customer['last_order_date'])): ?>
                                        <?= date('d/m/Y', strtotime($customer['last_order_date'])) ?>
                                        <?php if ($metric === 'at_risk'): ?>
                                            <br>
                                            <span class="text-xs text-red-600 font-semibold">
                                                <?= $customer['days_since_last_order'] ?? 0 ?> ngày trước
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Customer Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm border border-blue-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">💡 Top Buyers</h3>
            <p class="text-sm text-gray-700">Những khách hàng này là những người chi tiêu hàng đầu. Hãy tập trung vào việc giữ chân họ với các ưu đãi VIP đặc biệt.</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-sm border border-purple-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">❤️ Loyal Customers</h3>
            <p class="text-sm text-gray-700">Những khách hàng thường xuyên mua hàng. Tổ chức các chương trình loyalty để tăng giá trị LTV của họ.</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-sm border border-red-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">⚠️ At-Risk Customers</h3>
            <p class="text-sm text-gray-700">Những khách hàng chưa mua trong 90 ngày. Gửi email win-back hoặc offer đặc biệt để khôi phục mối quan hệ.</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm border border-green-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Growth Opportunity</h3>
            <p class="text-sm text-gray-700">Phân tích dữ liệu để tìm ra sản phẩm nào có tiềm năng phát triển cao và tối ưu hóa marketing strategy.</p>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
