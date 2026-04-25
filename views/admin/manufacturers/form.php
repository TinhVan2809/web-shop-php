<div class="max-w-xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold"><?= $manufacturer ? 'Sửa thương hiệu' : 'Thêm thương hiệu' ?></h2>
        </div>
        <a href="index.php?action=admin_manufacturers" class="text-gray-500 hover:text-black">Quay lại</a>
    </div>

    <form action="index.php?action=save_manufacturer" method="POST" class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
        <input type="hidden" name="manufacturer_id" value="<?= $manufacturer['manufacturer_id'] ?? '' ?>">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tên thương hiệu</label>
            <input type="text" name="manufacturer_name" value="<?= $manufacturer['manufacturer_name'] ?? '' ?>" required
                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                placeholder="Ví dụ: Nike, Adidas">
        </div>
        <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold hover:bg-gray-800 transition-all">
            Lưu thương hiệu
        </button>
    </form>
</div>
