<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="index.php?action=admin_banners" class="text-sm text-gray-500 hover:text-black flex items-center gap-1 mb-2">
            <i class="ri-arrow-left-line"></i> Quay lại danh sách
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= isset($banner) ? 'Cập nhật Banner' : 'Thêm Banner mới' ?></h2>
    </div>

    <form action="index.php?action=save_banner" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
        <input type="hidden" name="banenr_id" value="<?= $banner['banenr_id'] ?? '' ?>">
        <input type="hidden" name="current_img" value="<?= $banner['img'] ?? '' ?>">

        <!-- Upload Ảnh -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh Banner</label>
            <?php if (isset($banner['img'])): ?>
                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Ảnh hiện tại:</p>
                    <img src="../asset/<?= htmlspecialchars($banner['img']) ?>" class="w-full max-h-48 object-cover rounded-lg border">
                </div>
            <?php endif; ?>
            <div class="relative group">
                <input type="file" name="img" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer border border-gray-200 rounded-lg p-1" <?= isset($banner) ? '' : 'required' ?>>
            </div>
            <p class="mt-2 text-xs text-gray-400">Định dạng hỗ trợ: JPG, PNG, WEBP. Kích thước khuyên dùng: 1920x600px.</p>
        </div>

        <!-- Nội dung -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung hiển thị</label>
            <textarea name="content" rows="4" 
                class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-black focus:border-black outline-none transition-all placeholder:text-gray-300"
                placeholder="Nhập nội dung mô tả trên banner..."
                required><?= htmlspecialchars($banner['content'] ?? '') ?></textarea>
        </div>

        <!-- Đường dẫn (URL) -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Đường dẫn khi click vào "Shop Now"</label>
            <input type="text" name="url" value="<?= htmlspecialchars($banner['url'] ?? '') ?>"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-black focus:border-black outline-none transition-all placeholder:text-gray-300"
                placeholder="Ví dụ: index.php?action=category&id=1">
        </div>

        <!-- Nút bấm -->
        <div class="pt-4 flex gap-3">
            <button type="submit" class="bg-black text-white px-8 py-3 rounded-lg font-bold hover:bg-gray-800 transition-all flex-1">
                <i class="ri-save-line"></i> Lưu Banner
            </button>
            <a href="index.php?action=admin_banners" class="bg-gray-100 text-gray-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition-all text-center">
                Hủy
            </a>
        </div>
    </form>
</div>