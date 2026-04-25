<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold"><?= $product ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h2>
        <p class="text-gray-500 text-sm mt-1">Vui lòng nhập đầy đủ thông tin bên dưới</p>
    </div>
    <a href="index.php?action=admin_products" class="text-gray-500 hover:text-black flex items-center gap-2 font-medium">
        <i class="ri-arrow-left-line"></i> Quay lại
    </a>
</div>

<form action="index.php?action=save_product" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?? '' ?>">
    <input type="hidden" name="current_thumbnail" value="<?= $product['thumbnail'] ?? '' ?>">

    <!-- Left Column: Basic Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                <input type="text" name="name" value="<?= $product['name'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Ví dụ: Nike Air Max 2024">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả chi tiết</label>
                <textarea name="description" rows="10"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Nhập mô tả sản phẩm..."><?= $product['description'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Giá gốc (₫)</label>
                <input type="number" name="price" value="<?= $product['price'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Giá khuyến mãi (₫)</label>
                <input type="number" name="discount_price" value="<?= $product['discount_price'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Để trống nếu không có">
            </div>
        </div>
    </div>

    <!-- Right Column: Meta & Image -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện</label>
                <?php if(isset($product['thumbnail'])): ?>
                    <img src="/web-shop-php/asset/<?= $product['thumbnail'] ?>" class="w-full h-48 object-cover rounded-xl mb-4">
                <?php endif; ?>
                <input type="file" name="thumbnail" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-black file:text-white hover:file:bg-gray-800">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Danh mục</label>
                <select name="category_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= $cat['category_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Thương hiệu</label>
                <select name="manufacturer_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <?php foreach($manufacturers as $man): ?>
                        <option value="<?= $man['manufacturer_id'] ?>" <?= (isset($product['manufacturer_id']) && $product['manufacturer_id'] == $man['manufacturer_id']) ? 'selected' : '' ?>>
                            <?= $man['manufacturer_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mã SKU</label>
                <input type="text" name="sku" value="<?= $product['sku'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Vd: NIKE-AIR-01">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <option value="active" <?= (isset($product['status']) && $product['status'] == 'active') ? 'selected' : '' ?>>Đang kinh doanh</option>
                    <option value="inactive" <?= (isset($product['status']) && $product['status'] == 'inactive') ? 'selected' : '' ?>>Ngừng kinh doanh</option>
                    <option value="out_of_stock" <?= (isset($product['status']) && $product['status'] == 'out_of_stock') ? 'selected' : '' ?>>Hết hàng</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold shadow-xl shadow-black/10 hover:shadow-black/20 hover:scale-[1.01] transition-all">
            <?= $product ? 'Cập nhật sản phẩm' : 'Lưu sản phẩm' ?>
        </button>
    </div>
</form>
