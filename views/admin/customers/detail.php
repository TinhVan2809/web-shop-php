<?php
// Tier helper (same as list.php — kept self-contained for the render call)
function detailTier($orderCount) {
    if ($orderCount >= 5) return ['label' => 'VIP',          'class' => 'bg-amber-100 text-amber-700', 'icon' => 'ri-vip-crown-line'];
    if ($orderCount >= 2) return ['label' => 'Thường xuyên', 'class' => 'bg-blue-100 text-blue-700',   'icon' => 'ri-repeat-line'];
    return                        ['label' => 'Khách mới',   'class' => 'bg-green-100 text-green-700', 'icon' => 'ri-user-add-line'];
}
$tier = detailTier($order_count);

// Status label map
$orderStatusMap = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200'],
    'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'bg-blue-50 text-blue-700 border border-blue-200'],
    'shipping'  => ['label' => 'Đang giao',    'class' => 'bg-indigo-50 text-indigo-700 border border-indigo-200'],
    'completed' => ['label' => 'Hoàn thành',   'class' => 'bg-green-50 text-green-700 border border-green-200'],
    'cancelled' => ['label' => 'Đã hủy',       'class' => 'bg-red-50 text-red-700 border border-red-200'],
];
$payStatusMap = [
    'unpaid'   => ['label' => 'Chưa TT',  'class' => 'bg-gray-100 text-gray-600'],
    'paid'     => ['label' => 'Đã TT',    'class' => 'bg-green-100 text-green-700'],
    'failed'   => ['label' => 'Thất bại', 'class' => 'bg-red-100 text-red-700'],
    'refunded' => ['label' => 'Hoàn tiền','class' => 'bg-purple-100 text-purple-700'],
];
?>

<!-- ── Breadcrumb ─────────────────────────────────────────────────────── -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="index.php?action=admin_customers" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
        <i class="ri-team-line"></i> Khách hàng
    </a>
    <i class="ri-arrow-right-s-line"></i>
    <span class="text-gray-900 font-medium"><?= htmlspecialchars($customer['name']) ?></span>
</div>

<!-- ── Profile card ───────────────────────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- Left: identity -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center text-center">
        <?php
        $avatarColors = ['bg-indigo-100 text-indigo-700','bg-rose-100 text-rose-700','bg-amber-100 text-amber-700','bg-teal-100 text-teal-700'];
        $colorClass = $avatarColors[$customer['user_id'] % count($avatarColors)];
        $initial = strtoupper(mb_substr($customer['name'], 0, 1, 'UTF-8'));
        ?>
        <div class="w-20 h-20 rounded-2xl <?= $colorClass ?> flex items-center justify-center text-3xl font-bold mb-4">
            <?= $initial ?>
        </div>
        <h2 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($customer['name']) ?></h2>
        <p class="text-sm text-gray-500">@<?= htmlspecialchars($customer['username']) ?></p>

        <span class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $tier['class'] ?>">
            <i class="<?= $tier['icon'] ?>"></i> <?= $tier['label'] ?>
        </span>

        <?php if ($customer['status'] === 'locked'): ?>
        <span class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
            <i class="ri-lock-line"></i> Tài khoản bị khóa
        </span>
        <?php endif; ?>

        <div class="w-full mt-5 space-y-2.5 text-left">
            <?php if ($customer['gmail']): ?>
            <div class="flex items-center gap-2.5 text-sm text-gray-700">
                <i class="ri-mail-line text-gray-400 w-4 text-center"></i>
                <span class="truncate"><?= htmlspecialchars($customer['gmail']) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($customer['number_phone']): ?>
            <div class="flex items-center gap-2.5 text-sm text-gray-700">
                <i class="ri-phone-line text-gray-400 w-4 text-center"></i>
                <span><?= htmlspecialchars($customer['number_phone']) ?></span>
            </div>
            <?php endif; ?>
            <div class="flex items-center gap-2.5 text-sm text-gray-700">
                <i class="ri-calendar-line text-gray-400 w-4 text-center"></i>
                <span>Tham gia <?= date('d/m/Y', strtotime($customer['create_at'])) ?></span>
            </div>
        </div>

        <div class="mt-5 flex gap-2 w-full">
            <a href="index.php?action=user_form&id=<?= $customer['user_id'] ?>"
               class="flex-1 text-center py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
                <i class="ri-edit-line"></i> Sửa
            </a>
            <a href="index.php?action=toggle_user_status&id=<?= $customer['user_id'] ?>"
               class="flex-1 text-center py-2 border rounded-xl text-sm font-medium transition-all
                      <?= $customer['status'] === 'locked' ? 'border-green-200 text-green-600 hover:bg-green-50' : 'border-red-200 text-red-600 hover:bg-red-50' ?>">
                <i class="<?= $customer['status'] === 'locked' ? 'ri-lock-unlock-line' : 'ri-lock-line' ?>"></i>
                <?= $customer['status'] === 'locked' ? 'Mở khóa' : 'Khóa' ?>
            </a>
        </div>
    </div>

    <!-- Right: 3 stat cards -->
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-5 content-start">
        <!-- Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3">
                <i class="ri-shopping-bag-3-line text-lg"></i>
            </div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Tổng đơn hàng</p>
            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $order_count ?></p>
        </div>
        <!-- Total spent -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mb-3">
                <i class="ri-coins-line text-lg"></i>
            </div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Đã thanh toán</p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($paid_amount, 0, ',', '.') ?>₫</p>
            <?php if ($total_spent > $paid_amount): ?>
            <p class="text-xs text-gray-400 mt-1">Tổng: <?= number_format($total_spent, 0, ',', '.') ?>₫</p>
            <?php endif; ?>
        </div>
        <!-- Avg order -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mb-3">
                <i class="ri-bar-chart-line text-lg"></i>
            </div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Trung bình / đơn</p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($avg_order, 0, ',', '.') ?>₫</p>
        </div>

        <!-- Timeline summary -->
        <?php if ($order_count > 0):
            $first = end($orders);
            $latest = reset($orders);
        ?>
        <div class="sm:col-span-3 bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-5 text-white flex flex-col sm:flex-row gap-4 justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Đơn hàng đầu tiên</p>
                <p class="font-bold"><?= date('d/m/Y', strtotime($first['created_at'])) ?></p>
                <p class="text-xs text-gray-400"><?= htmlspecialchars($first['order_code']) ?></p>
            </div>
            <div class="flex items-center text-gray-500 text-lg">
                <i class="ri-arrow-right-line"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Đơn hàng gần nhất</p>
                <p class="font-bold"><?= date('d/m/Y', strtotime($latest['created_at'])) ?></p>
                <p class="text-xs text-gray-400"><?= htmlspecialchars($latest['order_code']) ?></p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Gắn bó</p>
                <p class="font-bold"><?= round((time() - strtotime($customer['create_at'])) / 86400) ?> ngày</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Order history ───────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-history-line text-indigo-500"></i>
            Lịch sử mua hàng
            <span class="ml-1 text-xs font-medium bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full"><?= $order_count ?> đơn</span>
        </h3>
        <a href="index.php?action=admin_orders" class="text-xs text-gray-400 hover:text-indigo-600 transition-colors">
            Xem tất cả đơn hàng <i class="ri-arrow-right-line"></i>
        </a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="py-16 flex flex-col items-center justify-center text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
            <i class="ri-shopping-bag-line text-xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium">Chưa có đơn hàng nào</p>
        <p class="text-sm text-gray-400 mt-1">Khách hàng này chưa thực hiện giao dịch</p>
    </div>
    <?php else: ?>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/80 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mã đơn</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Sản phẩm</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thanh toán</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Tổng tiền</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Xem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($orders as $order):
                $os = $orderStatusMap[$order['status']] ?? ['label' => $order['status'], 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'];
                $ps = $payStatusMap[$order['payment_status']] ?? ['label' => $order['payment_status'], 'class' => 'bg-gray-100 text-gray-600'];
            ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($order['order_code']) ?></p>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <p><?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                    <p class="text-xs text-gray-400"><?= date('H:i', strtotime($order['created_at'])) ?></p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-semibold text-gray-700"><?= $order['item_count'] ?></span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $os['class'] ?>">
                        <?= $os['label'] ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?= $ps['class'] ?>">
                        <?= $ps['label'] ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm">
                    <?= number_format($order['total_amount'], 0, ',', '.') ?>₫
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="index.php?action=order_detail&id=<?= $order['order_id'] ?>"
                       class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        <i class="ri-external-link-line"></i> Xem
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Back button -->
<div class="mt-6">
    <a href="index.php?action=admin_customers"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors">
        <i class="ri-arrow-left-line"></i> Quay lại danh sách khách hàng
    </a>
</div>
