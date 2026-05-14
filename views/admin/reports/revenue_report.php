<?php
$filter = $filter ?? 'month';
$date = $date ?? date('Y-m-d');
$revenue_data = $revenue_data ?? [];
$summary = $summary ?? [];
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-money-dollar-circle-line text-green-600"></i> Báo Cáo Doanh Thu
        </h1>
        <p class="text-gray-600 mt-2">Thống kê doanh thu theo ngày, tháng, năm</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter Type -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại báo cáo:</label>
                <div class="flex gap-2">
                    <a href="index.php?action=revenue_report&filter=day&date=<?= date('Y-m-d') ?>" 
                       class="px-4 py-2 rounded-lg border font-medium transition-all <?= $filter === 'day' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                        Theo Ngày
                    </a>
                    <a href="index.php?action=revenue_report&filter=month&date=<?= date('Y-m-d') ?>" 
                       class="px-4 py-2 rounded-lg border font-medium transition-all <?= $filter === 'month' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                        Theo Tháng
                    </a>
                    <a href="index.php?action=revenue_report&filter=year&date=<?= date('Y-m-d') ?>" 
                       class="px-4 py-2 rounded-lg border font-medium transition-all <?= $filter === 'year' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                        Theo Năm
                    </a>
                </div>
            </div>

            <!-- Date Picker -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày:</label>
                <input type="date" value="<?= $date ?>" onchange="location.href='index.php?action=revenue_report&filter=<?= $filter ?>&date=' + this.value" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
            <p class="text-sm text-gray-600 font-medium">Tổng Doanh Thu</p>
            <p class="text-3xl font-bold text-green-700 mt-2">
                <?= number_format($summary['total_revenue'] ?? 0, 0, ',', '.') ?>₫
            </p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
            <p class="text-sm text-gray-600 font-medium">Tổng Đơn Hàng</p>
            <p class="text-3xl font-bold text-blue-700 mt-2">
                <?= $summary['total_orders'] ?? 0 ?>
            </p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
            <p class="text-sm text-gray-600 font-medium">Đơn Hoàn Thành</p>
            <p class="text-3xl font-bold text-purple-700 mt-2">
                <?= $summary['completed_orders'] ?? 0 ?>
            </p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
            <p class="text-sm text-gray-600 font-medium">Giá Trị TB/Đơn</p>
            <p class="text-3xl font-bold text-orange-700 mt-2">
                <?= number_format($summary['avg_order_value'] ?? 0, 0, ',', '.') ?>₫
            </p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Chi Tiết Doanh Thu</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">Kỳ Tính</th>
                        <th class="px-6 py-4">Số Đơn Hàng</th>
                        <th class="px-6 py-4">Doanh Thu</th>
                        <th class="px-6 py-4">Giá Trị Trung Bình</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($revenue_data)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($revenue_data as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= $item['time_period'] ?></td>
                                <td class="px-6 py-4 text-gray-700"><?= $item['total_orders'] ?></td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    <?= number_format($item['total_revenue'], 0, ',', '.') ?>₫
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?= number_format($item['avg_order_value'], 0, ',', '.') ?>₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
