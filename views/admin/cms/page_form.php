<?php
$page = $page ?? null;
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-pages-line text-purple-600"></i> 
            <?= $page ? 'Chỉnh Sửa Trang' : 'Tạo Trang Mới' ?>
        </h1>
        <p class="text-gray-600 mt-2">Tạo hoặc chỉnh sửa trang tĩnh (About, Policy, etc.)</p>
    </div>

    <!-- Back Link -->
    <a href="index.php?action=admin_pages" class="text-purple-600 hover:underline mb-6 inline-block">
        ← Quay lại danh sách
    </a>

    <!-- Form -->
    <form method="POST" action="index.php?action=page_save" class="space-y-8">
        <input type="hidden" name="page_id" value="<?= $page['page_id'] ?? 0 ?>">

        <!-- Basic Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Thông Tin Cơ Bản</h2>
            
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu Đề <span class="text-red-600">*</span></label>
                    <input type="text" name="title" required 
                           value="<?= htmlspecialchars($page['title'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Nhập tiêu đề trang (Ví dụ: Về Chúng Tôi)">
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                    <input type="text" name="slug" 
                           value="<?= htmlspecialchars($page['slug'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Để trống để auto-generate">
                    <p class="text-xs text-gray-500 mt-1">Ví dụ: ve-chung-toi (sẽ được dùng trong URL)</p>
                </div>

                <!-- Meta Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description (SEO)</label>
                    <textarea name="meta_description" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Mô tả ngắn cho SEO (160 ký tự)"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Cảnh báo: <span id="meta-count">0</span>/160 ký tự</p>
                </div>

                <!-- Position & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vị Trí Hiển Thị</label>
                        <input type="number" name="position" value="<?= $page['position'] ?? 0 ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Số càng nhỏ càng hiển thị trước</p>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mt-8">
                            <input type="checkbox" name="is_published" value="1" 
                                   <?= ($page['is_published'] ?? 0) ? 'checked' : '' ?> 
                                   class="w-4 h-4 rounded border-gray-300">
                            <span class="text-sm font-medium text-gray-700">Công Khai</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Nội Dung Trang</h2>
            
            <textarea name="content" id="editor" class="w-full"><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 justify-end">
            <a href="index.php?action=admin_pages" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                Hủy
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-semibold flex items-center gap-2">
                <i class="ri-save-line"></i> <?= $page ? 'Cập Nhật' : 'Tạo Trang' ?>
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

// Meta description counter
document.querySelector('textarea[name="meta_description"]').addEventListener('input', function() {
    document.getElementById('meta-count').textContent = this.value.length;
});
</script>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
