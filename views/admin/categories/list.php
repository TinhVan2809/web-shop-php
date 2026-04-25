<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold">Quản lý danh mục</h2>
        <p class="text-gray-500 text-sm mt-1">Phân loại sản phẩm để khách hàng dễ tìm kiếm</p>
    </div>
    <a href="index.php?action=category_form" class="bg-black text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 hover:bg-gray-800 transition-all">
        <i class="ri-add-line"></i> Thêm danh mục
    </a>
</div>

<div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên danh mục</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($categories as $cat): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-400">#<?= $cat['category_id'] ?></td>
                    <td class="px-6 py-4 font-semibold text-gray-900"><?= $cat['category_name'] ?></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="index.php?action=category_form&id=<?= $cat['category_id'] ?>" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="index.php?action=delete_category&id=<?= $cat['category_id'] ?>" onclick="return confirm('Xóa danh mục này?')" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
