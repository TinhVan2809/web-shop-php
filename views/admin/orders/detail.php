<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold flex items-center gap-3">
            Chi tiết đơn hàng <span class="text-blue-600 font-mono">#<?= $order['order_code'] ?></span>
        </h2>
        <p class="text-gray-500 text-sm mt-1">Đặt lúc: <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
    </div>
    <div class="flex gap-3">
        <button onclick="window.print()" class="bg-white border border-gray-200 px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 hover:bg-gray-50 transition-all">
            <i class="ri-printer-line"></i> In hóa đơn
        </button>
        <a href="index.php?action=admin_orders" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition-all">
            Quay lại
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left: Order Items & Summary -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-bold">Danh sách sản phẩm</h3>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold">
                        <th class="px-6 py-4">Sản phẩm</th>
                        <th class="px-6 py-4">Đơn giá</th>
                        <th class="px-6 py-4">Số lượng</th>
                        <th class="px-6 py-4 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img src="/web-shop-php/asset/<?= $item['product_image'] ?>" class="w-12 h-12 rounded-lg object-cover">
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= $item['product_name'] ?></p>
                                        <p class="text-xs text-gray-500">SKU: <?= $item['sku'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                            <td class="px-6 py-4 text-sm font-medium">x<?= $item['quantity'] ?></td>
                            <td class="px-6 py-4 text-right font-bold"><?= number_format($item['total_price'], 0, ',', '.') ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="p-8 bg-gray-50/30 border-t border-gray-100">
                <div class="flex flex-col items-end gap-3 text-sm">
                    <div class="flex justify-between w-64">
                        <span class="text-gray-500">Tạm tính:</span>
                        <span class="font-medium"><?= number_format($order['subtotal'], 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="flex justify-between w-64">
                        <span class="text-gray-500">Giảm giá:</span>
                        <span class="text-red-500">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="flex justify-between w-64">
                        <span class="text-gray-500">Phí vận chuyển:</span>
                        <span class="font-medium"><?= number_format($order['shipping_fee'], 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="flex justify-between w-64 pt-3 border-t border-gray-200">
                        <span class="text-lg font-bold text-gray-900">Tổng cộng:</span>
                        <span class="text-lg font-bold text-red-600"><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Customer & Status Info -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold mb-6 flex items-center gap-2">
                <i class="ri-user-3-line text-blue-500"></i> Thông tin khách hàng
            </h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Người nhận</p>
                    <p class="font-semibold"><?= $order['recipient_name'] ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Số điện thoại</p>
                    <p class="font-semibold"><?= $order['recipient_phone'] ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Email tài khoản</p>
                    <p class="text-blue-600 underline"><?= $order['customer_email'] ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Địa chỉ giao hàng</p>
                    <p class="text-sm leading-relaxed">
                        <?= $order['specific_address'] ?>, <?= $order['ward_name'] ?>, <br>
                        <?= $order['district_name'] ?>, <?= $order['province_name'] ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold mb-6 flex items-center gap-2">
                <i class="ri-truck-line text-purple-500"></i> Trạng thái vận chuyển
            </h3>
            <form action="index.php?action=update_order_status" method="GET" class="space-y-4">
                <input type="hidden" name="action" value="update_order_status">
                <input type="hidden" name="id" value="<?= $order['order_id'] ?>">
                <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all font-bold">
                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>CHỜ XÁC NHẬN</option>
                    <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>ĐÃ XÁC NHẬN</option>
                    <option value="shipping" <?= $order['status'] == 'shipping' ? 'selected' : '' ?>>ĐANG GIAO</option>
                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>HOÀN THÀNH</option>
                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>ĐÃ HỦY</option>
                </select>
                <button type="submit" class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all">
                    Cập nhật trạng thái
                </button>
            </form>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold mb-6 flex items-center gap-2">
                <i class="ri-bank-card-line text-green-500"></i> Thanh toán
            </h3>
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                <span class="text-sm font-medium">Tình trạng:</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $order['payment_status'] == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= strtoupper($order['payment_status']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    aside, header, button, .no-print, a { display: none !important; }
    main { padding: 0 !important; margin: 0 !important; }
    .bg-gray-50 { background-color: transparent !important; }
    .shadow-sm { box-shadow: none !important; border: 1px solid #eee !important; }
}
</style>
