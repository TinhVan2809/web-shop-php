<?php
$blogs = $blogs ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ri-article-line text-blue-600"></i> Quản Lý Bài Viết Blog
            </h1>
            <p class="text-gray-600 mt-2">Tạo, chỉnh sửa và quản lý các bài viết blog</p>
        </div>
        <a href="index.php?action=blog_admin_form" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold flex items-center gap-2">
            <i class="ri-add-line"></i> Bài Viết Mới
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

    <!-- Blogs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">Tiêu Đề</th>
                        <th class="px-6 py-4">Danh Mục</th>
                        <th class="px-6 py-4">Tác Giả</th>
                        <th class="px-6 py-4">Trạng Thái</th>
                        <th class="px-6 py-4">Lượt Xem</th>
                        <th class="px-6 py-4">Ngày Tạo</th>
                        <th class="px-6 py-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($blogs)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Chưa có bài viết nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($blog['thumbnail'])): ?>
                                            <img src="/web-shop-php/asset/<?= htmlspecialchars($blog['thumbnail']) ?>" 
                                                 alt="<?= htmlspecialchars($blog['title']) ?>" 
                                                 class="w-10 h-10 rounded object-cover">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center">
                                                <i class="ri-image-2-line text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-medium text-gray-900 line-clamp-1"><?= htmlspecialchars($blog['title']) ?></p>
                                            <p class="text-xs text-gray-500">ID: <?= $blog['blog_id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($blog['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($blog['author']) ?></td>
                                <td class="px-6 py-4">
                                    <a href="index.php?action=blog_change_status&id=<?= $blog['blog_id'] ?>&status=<?= $blog['status'] === 'published' ? 'draft' : 'published' ?>" 
                                       class="inline-block px-3 py-1 text-xs font-bold rounded-full transition-colors
                                       <?php 
                                       if ($blog['status'] === 'published') echo 'bg-green-100 text-green-700 hover:bg-green-200';
                                       elseif ($blog['status'] === 'draft') echo 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200';
                                       else echo 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                                       ?>">
                                        <?php 
                                        echo $blog['status'] === 'published' ? 'Xuất Bản' : ($blog['status'] === 'draft' ? 'Nháp' : 'Lưu Trữ');
                                        ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="flex items-center gap-1">
                                        <i class="ri-eye-line"></i> <?= number_format($blog['views']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('d/m/Y', strtotime($blog['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="index.php?action=blog_admin_form&id=<?= $blog['blog_id'] ?>" 
                                           class="text-blue-600 hover:text-blue-800 font-semibold">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="index.php?action=blog_delete&id=<?= $blog['blog_id'] ?>" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?')"
                                           class="text-red-600 hover:text-red-800 font-semibold">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                        <a href="index.php?action=blog_detail&id=<?= $blog['blog_id'] ?>" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-800 font-semibold">
                                            <i class="ri-external-link-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-2 mt-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?action=admin_blogs&page=<?= $i ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-lg border font-bold transition-all
                   <?= $page === $i 
                       ? 'bg-blue-600 text-white border-blue-600 shadow-md' 
                       : 'bg-white text-gray-600 border-gray-200 hover:border-blue-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
