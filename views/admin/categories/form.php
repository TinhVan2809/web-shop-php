<div class="max-w-xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold"><?= $category ? 'Sửa danh mục' : 'Thêm danh mục' ?></h2>
        </div>
        <a href="index.php?action=admin_categories" class="text-gray-500 hover:text-black">Quay lại</a>
    </div>

    <form action="index.php?action=save_category" method="POST" class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?? '' ?>">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tên danh mục</label>
            <input type="text" name="category_name" value="<?= $category['category_name'] ?? '' ?>" required
                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                placeholder="Ví dụ: Giày thể thao">
        </div>
        <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold hover:bg-gray-800 transition-all">
            Lưu danh mục
        </button>
    </form>
</div>
