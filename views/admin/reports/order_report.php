<?php
$date_range = $date_range ?? '30';
$order_status = $order_status ?? [];
$orders = $orders ?? [];
$summary = $summary ?? [];
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-shopping-cart-2-line text-orange-600"></i> Báo Cáo Đơn Hàng
        </h1>
        <p class="text-gray-600 mt-2">Thống kê đơn hàng theo trạng thái và thanh toán</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex gap-2 flex-wrap">
            <a href="index.php?action=order_report&date_range=7" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $date_range == 7 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                7 Ngày
            </a>
            <a href="index.php?action=order_report&date_range=30" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $date_range == 30 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                30 Ngày
            </a>
            <a href="index.php?action=order_report&date_range=90" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $date_range == 90 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                90 Ngày
            </a>
            <a href="index.php?action=order_report&date_range=all" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $date_range == 'all' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                Tất Cả
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Tổng Đơn Hàng</p>
            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $summary['total_orders'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Hoàn Thành</p>
            <p class="text-3xl font-bold text-green-600 mt-2"><?= $summary['completed'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Đang Xử Lý</p>
            <p class="text-3xl font-bold text-orange-600 mt-2"><?= $summary['pending'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Tổng Doanh Thu</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">
                <?= number_format($summary['total_revenue'] ?? 0, 0, ',', '.') ?>₫
            </p>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Phân Bổ Theo Trạng Thái</h2>
            <div class="space-y-4">
                <?php 
                $status_map = [
                    'pending' => ['label' => 'Chờ xử lý', 'color' => 'orange'],
                    'confirmed' => ['label' => 'Đã xác nhận', 'color' => 'blue'],
                    'completed' => ['label' => 'Hoàn thành', 'color' => 'green'],
                    'cancelled' => ['label' => 'Huỷ', 'color' => 'red'],
                ];
                
                if (!empty($order_status)):
                    $total = array_sum(array_column($order_status, 'count'));
                    foreach ($order_status as $status):
                        $percentage = ($status['count'] / $total) * 100;
                        $color = $status_map[$status['status']]['color'] ?? 'gray';
                        $label = $status_map[$status['status']]['label'] ?? $status['status'];
                ?>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-900"><?= $label ?></span>
                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full"><?= $status['count'] ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-<?= $color ?>-500 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 ml-4"><?= round($percentage, 1) ?>%</span>
                    </div>
                <?php 
                    endforeach;
                endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Doanh Thu Theo Trạng Thái</h2>
            <div class="space-y-4">
                <?php 
                if (!empty($order_status)):
                    foreach ($order_status as $status):
                        $color = $status_map[$status['status']]['color'] ?? 'gray';
                        $label = $status_map[$status['status']]['label'] ?? $status['status'];
                ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900"><?= $label ?></p>
                            <p class="text-xs text-gray-500"><?= $status['count'] ?> đơn hàng</p>
                        </div>
                        <p class="font-bold text-<?= $color ?>-600">
                            <?= number_format($status['revenue'], 0, ',', '.') ?>₫
                        </p>
                    </div>
                <?php 
                    endforeach;
                endif; ?>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Danh Sách Đơn Hàng Gần Đây</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">Mã Đơn</th>
                        <th class="px-6 py-4">Khách Hàng</th>
                        <th class="px-6 py-4">Số Điện Thoại</th>
                        <th class="px-6 py-4">Tổng Tiền</th>
                        <th class="px-6 py-4">Trạng Thái</th>
                        <th class="px-6 py-4">Thanh Toán</th>
                        <th class="px-6 py-4">Ngày Tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Không có đơn hàng</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-blue-600"><?= $order['order_code'] ?></td>
                                <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($order['recipient_name']) ?></td>
                                <td class="px-6 py-4 text-gray-700"><?= $order['recipient_phone'] ?></td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <?= number_format($order['total_amount'], 0, ',', '.') ?>₫
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full 
                                        <?php 
                                        if ($order['status'] === 'completed') echo 'bg-green-100 text-green-700';
                                        elseif ($order['status'] === 'pending') echo 'bg-yellow-100 text-yellow-700';
                                        elseif ($order['status'] === 'confirmed') echo 'bg-blue-100 text-blue-700';
                                        else echo 'bg-red-100 text-red-700';
                                        ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full 
                                        <?= $order['payment_status'] === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
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
