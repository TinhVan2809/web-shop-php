<?php
$blog = $blog ?? null;
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-article-line text-blue-600"></i> 
            <?= $blog ? 'Chỉnh Sửa Bài Viết' : 'Tạo Bài Viết Mới' ?>
        </h1>
        <p class="text-gray-600 mt-2">Tạo hoặc chỉnh sửa nội dung bài viết blog</p>
    </div>

    <!-- Back Link -->
    <a href="index.php?action=admin_blogs" class="text-blue-600 hover:underline mb-6 inline-block">
        ← Quay lại danh sách
    </a>

    <!-- Form -->
    <form method="POST" action="index.php?action=blog_save" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?? 0 ?>">

        <!-- Basic Info Grid -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Thông Tin Cơ Bản</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu Đề <span class="text-red-600">*</span></label>
                    <input type="text" name="title" required 
                           value="<?= htmlspecialchars($blog['title'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nhập tiêu đề bài viết">
                </div>

                <!-- Slug -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                    <input type="text" name="slug" 
                           value="<?= htmlspecialchars($blog['slug'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Để trống để auto-generate">
                    <p class="text-xs text-gray-500 mt-1">Ví dụ: tieu-de-bai-viet</p>
                </div>

                <!-- Excerpt -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô Tả Ngắn</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Mô tả ngắn gọn về nội dung bài viết"><?= htmlspecialchars($blog['excerpt'] ?? '') ?></textarea>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh Mục</label>
                    <input type="text" name="category" 
                           value="<?= htmlspecialchars($blog['category'] ?? 'General') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Danh mục">
                </div>

                <!-- Author -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tác Giả</label>
                    <input type="text" name="author" 
                           value="<?= htmlspecialchars($blog['author'] ?? 'Admin') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Tên tác giả">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng Thái</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft" <?= ($blog['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Nháp</option>
                        <option value="published" <?= ($blog['status'] ?? '') === 'published' ? 'selected' : '' ?>>Xuất Bản</option>
                        <option value="archived" <?= ($blog['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Lưu Trữ</option>
                    </select>
                </div>
            </div>

            <!-- Thumbnail -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh Đại Diện</label>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="file" name="thumbnail" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (Max: 5MB)</p>
                    </div>
                    <?php if (!empty($blog['thumbnail'])): ?>
                        <div class="w-24 h-24">
                            <img src="/web-shop-php/asset/<?= htmlspecialchars($blog['thumbnail']) ?>" 
                                 alt="Thumbnail" class="w-full h-full object-cover rounded-lg border border-gray-200">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Nội Dung Bài Viết</h2>
            
            <textarea name="content" id="editor" class="w-full"><?= htmlspecialchars($blog['content'] ?? '') ?></textarea>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 justify-end">
            <a href="index.php?action=admin_blogs" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                Hủy
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold flex items-center gap-2">
                <i class="ri-save-line"></i> <?= $blog ? 'Cập Nhật' : 'Tạo Bài Viết' ?>
            </button>
        </div>
    </form>
</div>

<!-- TinyMCE Editor -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#editor',
    height: 400,
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | formatselect | bold italic underline strikethrough | link image media | alignleft aligncenter alignright | bullist numlist | blockquote code | removeformat',
    content_css: 'default',
    language: 'vi'
});
</script>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
