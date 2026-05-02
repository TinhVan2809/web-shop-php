<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Danh sách Banner</h2>
    <a href="index.php?action=banner_form" class="bg-black text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-gray-800 transition-all flex items-center gap-2">
        <i class="ri-add-line"></i> Thêm Banner mới
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Xem trước</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nội dung</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php if (empty($banners)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">Chưa có banner nào được tạo.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($banners as $banner): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-40 h-20 rounded-lg overflow-hidden border border-gray-100 shadow-sm">
                                <img src="../asset/<?= htmlspecialchars($banner['img']) ?>" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 line-clamp-2 max-w-md">
                                <?= htmlspecialchars($banner['content']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($banner['create_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="index.php?action=banner_form&id=<?= $banner['banenr_id'] ?>" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                    <i class="ri-edit-line text-lg"></i>
                                </a>
                                <a href="index.php?action=delete_banner&id=<?= $banner['banenr_id'] ?>" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa banner này?')"
                                   class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>