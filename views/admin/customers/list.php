<?php
// Helper: determine customer tier
function customerTier($orderCount, $joinDate) {
    $isNew = strtotime($joinDate) >= strtotime('-30 days');
    if ($orderCount >= 5)      return ['label' => 'VIP',       'class' => 'bg-amber-50 text-amber-700 border border-amber-200',   'dot' => 'bg-amber-500'];
    if ($orderCount >= 2)      return ['label' => 'Thường xuyên', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200', 'dot' => 'bg-blue-500'];
    if ($isNew)                return ['label' => 'Mới',       'class' => 'bg-green-50 text-green-700 border border-green-200',   'dot' => 'bg-green-500'];
    return                            ['label' => 'Thông thường','class' => 'bg-gray-50 text-gray-600 border border-gray-200',    'dot' => 'bg-gray-400'];
}
?>

<!-- ── Page header ─────────────────────────────────────────────────────── -->
<div class="flex justify-between items-start mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Quản lý khách hàng</h2>
        <p class="text-gray-500 text-sm mt-1">Danh sách khách hàng, thông tin liên hệ và lịch sử mua hàng</p>
    </div>
    <a href="index.php?action=admin_customers&type=all"
       class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
        <i class="ri-refresh-line"></i> Làm mới
    </a>
</div>

<!-- ── Stats cards ────────────────────────────────────────────────────── -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <!-- Total -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl flex-shrink-0">
            <i class="ri-team-line"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng khách hàng</p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5"><?= number_format($stats['total']) ?></p>
        </div>
    </div>
    <!-- New -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 text-xl flex-shrink-0">
            <i class="ri-user-add-line"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách hàng mới <span class="text-gray-400 font-normal">(30 ngày)</span></p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5"><?= number_format($stats['new']) ?></p>
        </div>
    </div>
    <!-- Frequent -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl flex-shrink-0">
            <i class="ri-repeat-line"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách thường xuyên</p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5"><?= number_format($stats['frequent']) ?></p>
        </div>
    </div>
    <!-- Revenue -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl flex-shrink-0">
            <i class="ri-money-dollar-circle-line"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Doanh thu (đã thanh toán)</p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5"><?= number_format($stats['total_revenue'], 0, ',', '.') ?>₫</p>
        </div>
    </div>
</div>

<!-- ── Filter bar ─────────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
    <!-- Tabs -->
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
        <?php
        $tabs = [
            'all'      => ['label' => 'Tất cả',      'icon' => 'ri-apps-line'],
            'new'      => ['label' => 'Khách mới',   'icon' => 'ri-user-add-line'],
            'frequent' => ['label' => 'Thường xuyên','icon' => 'ri-repeat-line'],
            'vip'      => ['label' => 'VIP',         'icon' => 'ri-vip-crown-line'],
        ];
        foreach ($tabs as $key => $tab):
            $active = ($type === $key);
        ?>
        <a href="index.php?action=admin_customers&type=<?= $key ?><?= $search ? '&search='.urlencode($search) : '' ?>"
           class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all
                  <?= $active ? 'bg-white shadow-sm text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' ?>">
            <i class="<?= $tab['icon'] ?>"></i>
            <?= $tab['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Search -->
    <form method="GET" action="index.php" class="flex items-center gap-2">
        <input type="hidden" name="action" value="admin_customers">
        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Tên, email, SĐT..."
                   class="pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:bg-white transition-all w-60">
        </div>
        <button type="submit"
                class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-700 transition-all">
            Tìm
        </button>
        <?php if ($search): ?>
        <a href="index.php?action=admin_customers&type=<?= $type ?>"
           class="px-3 py-2.5 text-gray-500 hover:text-red-500 transition-colors text-sm font-medium">
            <i class="ri-close-line"></i> Xóa
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- ── Customer Table ─────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <?php if (empty($customers)): ?>
    <div class="py-20 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
            <i class="ri-user-search-line text-2xl text-gray-400"></i>
        </div>
        <p class="font-semibold text-gray-700">Không tìm thấy khách hàng nào</p>
        <p class="text-sm text-gray-400 mt-1">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
    </div>
    <?php else: ?>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/80 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Khách hàng</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Liên hệ</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Phân loại</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Đơn hàng</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Tổng chi tiêu</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Đơn gần nhất</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tham gia</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($customers as $customer):
                $tier = customerTier($customer['order_count'], $customer['create_at']);
                $initials = strtoupper(mb_substr($customer['name'], 0, 1, 'UTF-8'));
                $avatarColors = ['bg-indigo-100 text-indigo-700','bg-rose-100 text-rose-700','bg-amber-100 text-amber-700','bg-teal-100 text-teal-700'];
                $colorClass = $avatarColors[$customer['user_id'] % count($avatarColors)];
            ?>
            <tr class="hover:bg-gray-50/60 transition-colors group">
                <!-- Name / avatar -->
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full <?= $colorClass ?> flex items-center justify-center font-bold text-sm flex-shrink-0">
                            <?= $initials ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($customer['name']) ?></p>
                            <p class="text-xs text-gray-400">@<?= htmlspecialchars($customer['username']) ?></p>
                        </div>
                    </div>
                </td>
                <!-- Contact -->
                <td class="px-6 py-4">
                    <div class="space-y-1">
                        <?php if ($customer['gmail']): ?>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                            <i class="ri-mail-line text-gray-400"></i>
                            <span class="truncate max-w-[160px]"><?= htmlspecialchars($customer['gmail']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($customer['number_phone']): ?>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                            <i class="ri-phone-line text-gray-400"></i>
                            <span><?= htmlspecialchars($customer['number_phone']) ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-gray-300">Chưa có SĐT</span>
                        <?php endif; ?>
                    </div>
                </td>
                <!-- Tier -->
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?= $tier['class'] ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $tier['dot'] ?>"></span>
                        <?= $tier['label'] ?>
                    </span>
                    <?php if ($customer['status'] === 'locked'): ?>
                    <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                        <i class="ri-lock-line"></i> Bị khóa
                    </span>
                    <?php endif; ?>
                </td>
                <!-- Orders -->
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                        <?= $customer['order_count'] >= 5 ? 'bg-amber-100 text-amber-700 font-bold' :
                           ($customer['order_count'] >= 2 ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-600') ?>
                        text-sm font-semibold">
                        <?= $customer['order_count'] ?>
                    </span>
                </td>
                <!-- Total spent -->
                <td class="px-6 py-4 text-right">
                    <p class="font-semibold text-gray-900 text-sm"><?= number_format($customer['total_spent'], 0, ',', '.') ?>₫</p>
                    <?php if ($customer['paid_amount'] > 0 && $customer['paid_amount'] != $customer['total_spent']): ?>
                    <p class="text-xs text-green-600"><?= number_format($customer['paid_amount'], 0, ',', '.') ?>₫ đã TT</p>
                    <?php endif; ?>
                </td>
                <!-- Last order -->
                <td class="px-6 py-4 text-sm text-gray-600">
                    <?php if ($customer['last_order_date']): ?>
                    <p><?= date('d/m/Y', strtotime($customer['last_order_date'])) ?></p>
                    <p class="text-xs text-gray-400"><?= date('H:i', strtotime($customer['last_order_date'])) ?></p>
                    <?php else: ?>
                    <span class="text-gray-300 text-xs">Chưa mua</span>
                    <?php endif; ?>
                </td>
                <!-- Join date -->
                <td class="px-6 py-4 text-sm text-gray-600">
                    <p><?= date('d/m/Y', strtotime($customer['create_at'])) ?></p>
                    <p class="text-xs text-gray-400"><?= date('H:i', strtotime($customer['create_at'])) ?></p>
                </td>
                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                    <a href="index.php?action=admin_customer_detail&id=<?= $customer['user_id'] ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-900 text-white rounded-lg text-xs font-medium
                              hover:bg-indigo-600 transition-all opacity-0 group-hover:opacity-100">
                        <i class="ri-eye-line"></i> Chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Table footer: count -->
    <div class="px-6 py-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500 bg-gray-50/40">
        <span>Hiển thị <strong><?= count($customers) ?></strong> khách hàng</span>
        <?php if ($type !== 'all' || $search): ?>
        <a href="index.php?action=admin_customers" class="text-indigo-600 hover:underline font-medium">
            Xem tất cả →
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── Activity Insight ───────────────────────────────────────────────── -->
<?php
$pct_new = $stats['total'] > 0 ? round($stats['new'] / $stats['total'] * 100) : 0;
$pct_freq = $stats['total'] > 0 ? round($stats['frequent'] / $stats['total'] * 100) : 0;
?>
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Composition bar -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="ri-pie-chart-line text-indigo-500"></i> Phân bố khách hàng
        </h3>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> Khách mới (30 ngày)</span>
                    <span class="font-bold"><?= $stats['new'] ?> (<?= $pct_new ?>%)</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-400 rounded-full transition-all" style="width:<?= $pct_new ?>%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span> Thường xuyên (≥ 2 đơn)</span>
                    <span class="font-bold"><?= $stats['frequent'] ?> (<?= $pct_freq ?>%)</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-400 rounded-full transition-all" style="width:<?= $pct_freq ?>%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span> Còn lại</span>
                    <span class="font-bold"><?= max(0, $stats['total'] - $stats['new'] - $stats['frequent']) ?></span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gray-300 rounded-full transition-all"
                         style="width:<?= max(0, 100 - $pct_new - $pct_freq) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick tips -->
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white">
        <h3 class="text-sm font-bold mb-4 flex items-center gap-2">
            <i class="ri-lightbulb-line"></i> Gợi ý chăm sóc khách hàng
        </h3>
        <ul class="space-y-2.5 text-sm">
            <li class="flex items-start gap-2">
                <i class="ri-checkbox-circle-fill text-indigo-300 mt-0.5 flex-shrink-0"></i>
                <span class="text-indigo-100">Gửi email chào mừng cho <strong class="text-white"><?= $stats['new'] ?></strong> khách hàng mới trong 30 ngày qua.</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="ri-checkbox-circle-fill text-indigo-300 mt-0.5 flex-shrink-0"></i>
                <span class="text-indigo-100">Tặng voucher ưu đãi cho <strong class="text-white"><?= $stats['frequent'] ?></strong> khách thường xuyên.</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="ri-checkbox-circle-fill text-indigo-300 mt-0.5 flex-shrink-0"></i>
                <span class="text-indigo-100">Lọc tab <strong class="text-white">VIP</strong> để tìm khách hàng mua từ 5 đơn trở lên và ưu tiên chăm sóc.</span>
            </li>
        </ul>
    </div>
</div>
