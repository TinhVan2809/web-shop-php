<?php
$pages = $pages ?? [];
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ri-pages-line text-purple-600"></i> Quản Lý Trang Tĩnh
            </h1>
            <p class="text-gray-600 mt-2">Quản lý: Về chúng tôi, Chính sách, Điều khoản, v.v.</p>
        </div>
        <a href="index.php?action=page_admin_form" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold flex items-center gap-2">
            <i class="ri-add-line"></i> Trang Mới
        </a>
    </div>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-6 flex items-center gap-2">
            <i class="ri-checkbox-circle-line text-xl"></i>
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Pages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($pages)): ?>
            <div class="col-span-full text-center py-12">
                <i class="ri-pages-line text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Chưa có trang nào. Hãy tạo trang mới!</p>
            </div>
        <?php else: ?>
            <?php foreach ($pages as $page): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 flex-1"><?= htmlspecialchars($page['title']) ?></h3>
                        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full 
                            <?= $page['is_published'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?>">
                            <?= $page['is_published'] ? 'Công Khai' : 'Ẩn' ?>
                        </span>
                    </div>

                    <!-- Slug -->
                    <p class="text-sm text-gray-500 mb-4">
                        <code class="bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($page['slug']) ?></code>
                    </p>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        <?= strlen($page['meta_description']) > 100 ? substr($page['meta_description'], 0, 100) . '...' : $page['meta_description'] ?>
                    </p>

                    <!-- Meta -->
                    <div class="text-xs text-gray-500 mb-4 space-y-1">
                        <p>Vị trí: <span class="font-semibold"><?= $page['position'] ?></span></p>
                        <p>Cập nhật: <span class="font-semibold"><?= date('d/m/Y H:i', strtotime($page['updated_at'])) ?></span></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4 border-t border-gray-200">
                        <a href="index.php?action=page_admin_form&id=<?= $page['page_id'] ?>" 
                           class="flex-1 text-center text-blue-600 hover:text-blue-800 font-semibold py-2 hover:bg-blue-50 rounded transition-colors">
                            <i class="ri-edit-line"></i> Sửa
                        </a>
                        <a href="index.php?action=page_delete&id=<?= $page['page_id'] ?>" 
                           onclick="return confirm('Bạn chắc chắn muốn xóa?')"
                           class="flex-1 text-center text-red-600 hover:text-red-800 font-semibold py-2 hover:bg-red-50 rounded transition-colors">
                            <i class="ri-delete-bin-line"></i> Xóa
                        </a>
                        <a href="index.php?action=page_view&slug=<?= $page['slug'] ?>" 
                           target="_blank"
                           class="flex-1 text-center text-green-600 hover:text-green-800 font-semibold py-2 hover:bg-green-50 rounded transition-colors">
                            <i class="ri-external-link-line"></i> Xem
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
