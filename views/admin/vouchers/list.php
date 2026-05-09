<?php
$vouchers = $vouchers ?? [];

function voucherJoinLabels($rows, $key) {
	if (empty($rows) || !is_array($rows)) return [];

	$labels = [];
	foreach ($rows as $row) {
		if (is_array($row) && isset($row[$key]) && $row[$key] !== '') {
			$labels[] = $row[$key];
		} elseif (is_string($row) && trim($row) !== '') {
			$labels[] = trim($row);
		}
	}

	return $labels;
}

function voucherStatusBadge($status) {
	$map = [
		'active' => ['bg-green-50 text-green-600', 'ĐANG HOẠT ĐỘNG'],
		'inactive' => ['bg-gray-50 text-gray-600', 'TẠM TẮT'],
		'expired' => ['bg-red-50 text-red-600', 'HẾT HẠN'],
	];

	return $map[$status] ?? ['bg-gray-50 text-gray-600', 'KHÔNG XÁC ĐỊNH'];
}

function voucherTypeLabel($type) {
	return $type === 'fixed' ? 'Số tiền cố định' : 'Phần trăm';
}

function voucherScopeText($voucher) {
	$categoryLabels = [];
	$productLabels = [];

	if (isset($voucher['categories']) && is_array($voucher['categories'])) {
		$categoryLabels = voucherJoinLabels($voucher['categories'], 'category_name');
	}
	if (isset($voucher['products']) && is_array($voucher['products'])) {
		$productLabels = voucherJoinLabels($voucher['products'], 'name');
	}

	if (empty($categoryLabels) && !empty($voucher['category_names'])) {
		$categoryLabels = is_array($voucher['category_names']) ? $voucher['category_names'] : explode(',', (string)$voucher['category_names']);
	}
	if (empty($productLabels) && !empty($voucher['product_names'])) {
		$productLabels = is_array($voucher['product_names']) ? $voucher['product_names'] : explode(',', (string)$voucher['product_names']);
	}

	$parts = [];
	if (!empty($categoryLabels)) $parts[] = 'DM: ' . implode(', ', array_slice($categoryLabels, 0, 2)) . (count($categoryLabels) > 2 ? '...' : '');
	if (!empty($productLabels)) $parts[] = 'SP: ' . implode(', ', array_slice($productLabels, 0, 2)) . (count($productLabels) > 2 ? '...' : '');

	return empty($parts) ? 'Áp dụng chung' : implode(' | ', $parts);
}

function voucherDiscountText($voucher) {
	$type = $voucher['discount_type'] ?? 'percent';
	$value = (float)($voucher['discount_value'] ?? 0);

	if ($type === 'fixed') {
		return number_format($value, 0, ',', '.') . '₫';
	}

	$text = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
	return $text . '%';
}

function voucherValidityText($voucher) {
	$start = !empty($voucher['start_date']) ? date('d/m/Y H:i', strtotime($voucher['start_date'])) : '---';
	$end = !empty($voucher['end_date']) ? date('d/m/Y H:i', strtotime($voucher['end_date'])) : '---';
	return $start . ' → ' . $end;
}
?>

<div class="flex justify-between items-center mb-8">
	<div>
		<h2 class="text-2xl font-bold">Danh sách voucher</h2>
		<p class="text-gray-500 text-sm mt-1">Quản lý mã giảm giá, điều kiện áp dụng và giới hạn sử dụng</p>
	</div>
	<a href="index.php?action=voucher_form" class="bg-black text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 hover:bg-gray-800 transition-all">
		<i class="ri-coupon-3-line"></i>
		Thêm voucher
	</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
	<table class="w-full text-left border-collapse">
		<thead>
			<tr class="bg-gray-50 border-b border-gray-100">
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Voucher</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mô tả</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Áp dụng</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Giảm giá</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Điều kiện</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thời gian</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
				<th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
			</tr>
		</thead>
		<tbody class="divide-y divide-gray-100">
			<?php if (empty($vouchers)): ?>
				<tr>
					<td colspan="7" class="px-6 py-12 text-center text-gray-400">Chưa có voucher nào.</td>
				</tr>
			<?php else: ?>
				<?php foreach ($vouchers as $voucher): ?>
					<?php [$badgeClass, $badgeText] = voucherStatusBadge($voucher['status'] ?? 'inactive'); ?>
					<tr class="hover:bg-gray-50/50 transition-colors">
						<td class="px-6 py-4">
							<div class="flex flex-col gap-1">
								<p class="font-mono font-bold text-blue-600 text-sm">#<?= htmlspecialchars($voucher['code'] ?? '') ?></p>
							</div>
						</td>
						<td class="px-6 py-4">
							<p class="text-xs text-gray-500 line-clamp-2 max-w-sm"><?= htmlspecialchars($voucher['description'] ?? 'Không có mô tả') ?></p>
						</td>
						<td class="px-6 py-4">
							<p class="text-sm text-gray-700 max-w-sm"><?= htmlspecialchars(voucherScopeText($voucher)) ?></p>
						</td>
						<td class="px-6 py-4">
							<div class="flex flex-col gap-1">
								<p class="text-sm font-bold text-gray-900"><?= htmlspecialchars(voucherDiscountText($voucher)) ?></p>
								<p class="text-xs text-gray-500">Loại: <?= voucherTypeLabel($voucher['discount_type'] ?? 'percent') ?></p>
							</div>
						</td>
						<td class="px-6 py-4 text-sm text-gray-600">
							<div class="flex flex-col gap-1">
								<span>Đơn tối thiểu: <?= number_format((float)($voucher['min_order_value'] ?? 0), 0, ',', '.') ?>₫</span>
								<span>Giới hạn: <?= isset($voucher['usage_limit']) && $voucher['usage_limit'] !== '' ? (int)$voucher['usage_limit'] : 'Không giới hạn' ?></span>
								<span>Đã dùng: <?= (int)($voucher['used_count'] ?? 0) ?></span>
							</div>
						</td>
						<td class="px-6 py-4 text-sm text-gray-600">
							<?= htmlspecialchars(voucherValidityText($voucher)) ?>
						</td>
						<td class="px-6 py-4">
							<span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $badgeClass ?>">
								<?= $badgeText ?>
							</span>
						</td>
						<td class="px-6 py-4 text-right">
							<div class="flex justify-end gap-2">
								<a href="index.php?action=voucher_form&id=<?= $voucher['voucher_id'] ?>"
								   class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Sửa">
									<i class="ri-edit-line"></i>
								</a>
								<a href="index.php?action=delete_voucher&id=<?= $voucher['voucher_id'] ?>"
								   onclick="return confirm('Xóa voucher này?')"
								   class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
									<i class="ri-delete-bin-line"></i>
								</a>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
