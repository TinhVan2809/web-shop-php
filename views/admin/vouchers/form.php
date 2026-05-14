<?php
$voucher = $voucher ?? [];
$categories = $categories ?? [];
$products = $products ?? [];
$voucherCategoryIds = $voucherCategoryIds ?? ($selectedCategoryIds ?? ($voucher['category_ids'] ?? []));
$voucherProductIds = $voucherProductIds ?? ($selectedProductIds ?? ($voucher['product_ids'] ?? []));

if (!is_array($voucherCategoryIds)) $voucherCategoryIds = [];
if (!is_array($voucherProductIds)) $voucherProductIds = [];

function voucherValue($array, $key, $default = '') {
	return isset($array[$key]) ? $array[$key] : $default;
}

function voucherChecked($haystack, $value) {
	return in_array((string)$value, array_map('strval', $haystack), true) ? 'checked' : '';
}
?>

<div class="flex justify-between items-center mb-8">
	<div>
		<h2 class="text-2xl font-bold"><?= !empty($voucher['voucher_id']) ? 'Sửa voucher' : 'Thêm voucher' ?></h2>
		<p class="text-gray-500 text-sm mt-1">Tạo mã giảm giá và gắn cho danh mục / sản phẩm cụ thể</p>
	</div>
	<a href="index.php?action=admin_vouchers" class="text-gray-500 hover:text-black flex items-center gap-2 font-medium">
		<i class="ri-arrow-left-line"></i> Quay lại
	</a>
</div>

<?php if (!empty($error)): ?>
	<div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-red-700 flex items-center gap-3">
		<i class="ri-error-warning-line text-lg"></i>
		<span class="font-medium"><?= htmlspecialchars($error) ?></span>
	</div>
<?php endif; ?>

<form action="index.php?action=save_voucher" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
	<input type="hidden" name="voucher_id" value="<?= voucherValue($voucher, 'voucher_id') ?>">

	<div class="lg:col-span-2 space-y-6">
		<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Mã voucher</label>
					<input type="text" name="code" value="<?= htmlspecialchars(voucherValue($voucher, 'code')) ?>" required
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all uppercase"
						placeholder="SALE2026">
				</div>
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái</label>
					<select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
						<option value="active" <?= voucherValue($voucher, 'status', 'active') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
						<option value="inactive" <?= voucherValue($voucher, 'status') === 'inactive' ? 'selected' : '' ?>>Tạm tắt</option>
						<option value="expired" <?= voucherValue($voucher, 'status') === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
					</select>
				</div>
			</div>

			<div>
				<label class="block text-sm font-bold text-gray-700 mb-2">Mô tả</label>
				<textarea name="description" rows="4"
					class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
					placeholder="Mô tả ngắn về chương trình giảm giá..."><?= htmlspecialchars(voucherValue($voucher, 'description')) ?></textarea>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Kiểu giảm giá</label>
					<select name="discount_type" id="discount-type" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
						<option value="percent" <?= voucherValue($voucher, 'discount_type', 'percent') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
						<option value="fixed" <?= voucherValue($voucher, 'discount_type') === 'fixed' ? 'selected' : '' ?>>Số tiền cố định</option>
					</select>
				</div>
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Giá trị giảm</label>
					<input type="number" step="0.01" min="0" max="100" id="discount-value" name="discount_value" value="<?= htmlspecialchars(voucherValue($voucher, 'discount_value')) ?>" required
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
						placeholder="Ví dụ: 10 hoặc 50000">
					<p id="discount-value-help" class="text-xs text-gray-400 mt-1">Với voucher %: tối đa 100.</p>
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Giảm tối đa</label>
					<input type="number" step="0.01" min="0" name="max_discount" value="<?= htmlspecialchars(voucherValue($voucher, 'max_discount')) ?>"
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
						placeholder="Để trống nếu không giới hạn">
				</div>
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Đơn hàng tối thiểu</label>
					<input type="number"min="1" name="min_order_value" value="<?= htmlspecialchars(voucherValue($voucher, 'min_order_value', '1')) ?>"
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
						placeholder="0">
				</div>
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Giới hạn lượt dùng</label>
					<input type="number" min="0" name="usage_limit" value="<?= htmlspecialchars(voucherValue($voucher, 'usage_limit')) ?>"
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
						placeholder="Để trống nếu không giới hạn">
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Ngày bắt đầu</label>
					<input type="datetime-local" name="start_date" value="<?= !empty($voucher['start_date']) ? (strpos($voucher['start_date'], 'T') !== false ? $voucher['start_date'] : date('Y-m-d\TH:i', strtotime($voucher['start_date']))) : '' ?>"
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
				</div>
				<div>
					<label class="block text-sm font-bold text-gray-700 mb-2">Ngày kết thúc</label>
					<input type="datetime-local" name="end_date" value="<?= !empty($voucher['end_date']) ? (strpos($voucher['end_date'], 'T') !== false ? $voucher['end_date'] : date('Y-m-d\TH:i', strtotime($voucher['end_date']))) : '' ?>"
						class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
				</div>
			</div>
		</div>

		<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
			<div class="flex justify-between items-center mb-5">
				<div>
					<h3 class="text-lg font-bold">Áp dụng cho danh mục</h3>
					<p class="text-sm text-gray-500">Chọn một hoặc nhiều danh mục để giới hạn voucher</p>
				</div>
				<span class="text-xs font-bold text-gray-400 uppercase">voucher_categories</span>
			</div>

			<?php if (empty($categories)): ?>
				<p class="text-sm text-gray-400 py-4">Chưa có danh mục nào để chọn.</p>
			<?php else: ?>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-72 overflow-auto pr-1">
					<?php foreach ($categories as $category): ?>
						<label class="category-item flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-black transition-colors cursor-pointer">
							<input type="checkbox" name="category_ids[]" value="<?= $category['category_id'] ?>" <?= voucherChecked($voucherCategoryIds, $category['category_id']) ?> class="h-4 w-4 category-checkbox">
							<span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($category['category_name']) ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
			<div class="flex justify-between items-center mb-5">
				<div>
					<h3 class="text-lg font-bold">Áp dụng cho sản phẩm</h3>
					<p class="text-sm text-gray-500">Chọn một hoặc nhiều sản phẩm để giới hạn voucher</p>
				</div>
				<div class="flex items-center gap-3">
					<button type="button" id="select-visible-products" class="text-xs font-bold text-black bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg transition-colors">Chọn tất cả</button>
					<button type="button" id="clear-visible-products" class="text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors">Bỏ chọn</button>
					<span class="text-xs font-bold text-gray-400 uppercase">voucher_products</span>
				</div>
			</div>
			<p class="text-xs text-gray-400 mb-4">Danh sách sản phẩm sẽ tự lọc theo danh mục đã chọn.</p>

			<?php if (empty($products)): ?>
				<p class="text-sm text-gray-400 py-4">Chưa có sản phẩm nào để chọn.</p>
			<?php else: ?>
				<div id="voucher-products-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-auto pr-1">
					<?php foreach ($products as $product): ?>
						<label class="product-item flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-black transition-colors cursor-pointer" data-category-id="<?= (int)($product['category_id'] ?? 0) ?>">
							<input type="checkbox" name="product_ids[]" value="<?= $product['product_id'] ?>" <?= voucherChecked($voucherProductIds, $product['product_id']) ?> class="h-4 w-4 product-checkbox">
							<span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($product['name']) ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="space-y-6">
		<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-4 sticky top-6">
			<h3 class="text-lg font-bold">Thông tin nhanh</h3>
			<div class="space-y-3 text-sm text-gray-600">
				<div class="flex justify-between gap-4">
					<span>Mã voucher</span>
					<span class="font-semibold text-gray-900"><?= $voucher['code'] ?? 'Mới' ?></span>
				</div>
				<div class="flex justify-between gap-4">
					<span>Đã dùng</span>
					<span class="font-semibold text-gray-900"><?= (int)($voucher['used_count'] ?? 0) ?></span>
				</div>
				<div class="flex justify-between gap-4">
					<span>Loại giảm</span>
					<span class="font-semibold text-gray-900"><?= ($voucher['discount_type'] ?? 'percent') === 'fixed' ? 'Cố định' : 'Phần trăm' ?></span>
				</div>
				<div class="flex justify-between gap-4">
					<span>Phạm vi</span>
					<span class="font-semibold text-gray-900">Danh mục / sản phẩm</span>
				</div>
			</div>

			<button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold shadow-xl shadow-black/10 hover:shadow-black/20 hover:scale-[1.01] transition-all">
				<?= !empty($voucher['voucher_id']) ? 'Cập nhật voucher' : 'Lưu voucher' ?>
			</button>
		</div>

		<div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 text-sm text-amber-900">
			<p class="font-bold mb-2">Lưu ý</p>
			<p>Nếu không chọn danh mục hoặc sản phẩm nào, voucher có thể được hiểu là áp dụng chung. Phần xử lý lưu cần ghi dữ liệu vào `voucher_categories` và `voucher_products` tương ứng.</p>
		</div>
	</div>
</form>

<script>
(function () {
	const discountTypeSelect = document.getElementById('discount-type');
	const discountValueInput = document.getElementById('discount-value');
	const discountValueHelp = document.getElementById('discount-value-help');
	const categoryCheckboxes = Array.from(document.querySelectorAll('.category-checkbox'));
	const productItems = Array.from(document.querySelectorAll('.product-item'));
	const productCheckboxes = Array.from(document.querySelectorAll('.product-checkbox'));
	const selectVisibleBtn = document.getElementById('select-visible-products');
	const clearVisibleBtn = document.getElementById('clear-visible-products');

	function clampDiscountValue() {
		if (!discountTypeSelect || !discountValueInput) return;

		if (discountTypeSelect.value === 'percent') {
			discountValueInput.max = '100';
			discountValueHelp.textContent = 'Với voucher %: tối đa 100.';
			if (Number(discountValueInput.value) > 100) {
				discountValueInput.value = 100;
			}
		} else {
			discountValueInput.removeAttribute('max');
			discountValueHelp.textContent = 'Với voucher số tiền cố định: nhập số tiền giảm.';
		}
	}

	function getSelectedCategoryIds() {
		return categoryCheckboxes
			.filter((checkbox) => checkbox.checked)
			.map((checkbox) => String(checkbox.value));
	}

	function syncVisibleProducts(resetVisibleSelection = false) {
		const selectedCategoryIds = getSelectedCategoryIds();
		const shouldFilter = selectedCategoryIds.length > 0;

		productItems.forEach((item) => {
			const itemCategoryId = String(item.dataset.categoryId || '0');
			const isVisible = !shouldFilter || selectedCategoryIds.includes(itemCategoryId);
			item.classList.toggle('hidden', !isVisible);

			if (resetVisibleSelection && isVisible) {
				const checkbox = item.querySelector('.product-checkbox');
				if (checkbox) {
					checkbox.checked = false;
				}
			}
		});
	}

	function toggleVisibleProducts(checked) {
		productItems.forEach((item) => {
			if (item.classList.contains('hidden')) {
				return;
			}
			const checkbox = item.querySelector('.product-checkbox');
			if (checkbox) {
				checkbox.checked = checked;
			}
		});
	}

	categoryCheckboxes.forEach((checkbox) => {
		checkbox.addEventListener('change', function () {
			syncVisibleProducts(true);
		});
	});

	if (selectVisibleBtn) {
		selectVisibleBtn.addEventListener('click', function () {
			toggleVisibleProducts(true);
		});
	}

	if (clearVisibleBtn) {
		clearVisibleBtn.addEventListener('click', function () {
			toggleVisibleProducts(false);
		});
	}

	if (discountTypeSelect) {
		discountTypeSelect.addEventListener('change', clampDiscountValue);
	}

	if (discountValueInput) {
		discountValueInput.addEventListener('input', function () {
			if (discountTypeSelect && discountTypeSelect.value === 'percent' && Number(this.value) > 100) {
				this.value = 100;
			}
		});
	}

	clampDiscountValue();
	syncVisibleProducts(false);
})();
</script>
