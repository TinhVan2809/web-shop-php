<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold">Quản lý đơn hàng</h2>
        <p class="text-gray-500 text-sm mt-1">Theo dõi tiến độ và xử lý vận chuyển</p>
    </div>
    <div class="flex gap-3">
        <div class="flex bg-gray-100 p-1 rounded-xl gap-1">
            <a href="index.php?action=admin_orders" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= !$current_status ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' ?>">TẤT CẢ</a>
            <a href="index.php?action=admin_orders&status=pending" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $current_status == 'pending' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' ?>">CHỜ XỬ LÝ</a>
            <a href="index.php?action=admin_orders&status=confirmed" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $current_status == 'confirmed' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' ?>">ĐÃ XÁC NHẬN</a>
            <a href="index.php?action=admin_orders&status=shipping" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $current_status == 'shipping' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' ?>">ĐANG GIAO</a>
            <a href="index.php?action=admin_orders&status=completed" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $current_status == 'completed' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' ?>">HOÀN THÀNH</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mã đơn</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Khách hàng</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono font-bold text-blue-600">#<?= $order['order_code'] ?></span>
                        <p class="text-[10px] text-gray-400 mt-0.5"><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900"><?= $order['recipient_name'] ?></p>
                        <p class="text-xs text-gray-500"><?= $order['recipient_phone'] ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900"><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</p>
                        <span class="text-[10px] text-gray-400"><?= strtoupper($order['payment_status']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $statusMap = [
                            'pending' => ['bg-orange-50 text-orange-600', 'CHỜ XÁC NHẬN'],
                            'confirmed' => ['bg-blue-50 text-blue-600', 'ĐÃ XÁC NHẬN'],
                            'shipping' => ['bg-purple-50 text-purple-600', 'ĐANG GIAO'],
                            'completed' => ['bg-green-50 text-green-600', 'HOÀN THÀNH'],
                            'cancelled' => ['bg-red-50 text-red-600', 'ĐÃ HỦY']
                        ];
                        $st = $statusMap[$order['status']] ?? ['bg-gray-50 text-gray-600', 'KHOÔNG XÁC ĐỊNH'];
                        ?>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $st[0] ?>">
                            <?= $st[1] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 group relative">
                            <a href="index.php?action=order_detail&id=<?= $order['order_id'] ?>" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-black hover:bg-gray-100 rounded-lg transition-all" title="Xem chi tiết">
                                <i class="ri-eye-line"></i>
                            </a>
                            <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Cập nhật trạng thái nhanh">
                                <i class="ri-refresh-line"></i>
                            </button>
                            <!-- Dropdown đơn giản khi hover -->
                            <div class="absolute right-0 bottom-full mb-2 hidden group-hover:flex flex-col bg-white border border-gray-100 shadow-xl rounded-xl p-2 z-50 w-40">
                                <a href="index.php?action=update_order_status&id=<?= $order['order_id'] ?>&status=confirmed" class="px-3 py-2 hover:bg-blue-50 text-blue-600 text-xs font-bold rounded-lg transition-colors">XÁC NHẬN</a>
                                <a href="index.php?action=update_order_status&id=<?= $order['order_id'] ?>&status=shipping" class="px-3 py-2 hover:bg-purple-50 text-purple-600 text-xs font-bold rounded-lg transition-colors">ĐANG GIAO</a>
                                <a href="index.php?action=update_order_status&id=<?= $order['order_id'] ?>&status=completed" class="px-3 py-2 hover:bg-green-50 text-green-600 text-xs font-bold rounded-lg transition-colors">HOÀN THÀNH</a>
                                <a href="index.php?action=update_order_status&id=<?= $order['order_id'] ?>&status=cancelled" class="px-3 py-2 hover:bg-red-50 text-red-600 text-xs font-bold rounded-lg transition-colors">HỦY ĐƠN</a>
                            </div>
                        </div>
                    </td>
                </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</div>
