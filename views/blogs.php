<?php
// Initialize variables for static analysis
$categories = $categories ?? [];
$blogs = $blogs ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
?>
<main class="container mx-auto px-4 md:px-7 py-20 mt-20">
    <!-- Hero Section -->
    <div class="mb-16 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Blog & Insights</h1>
        <p class="text-lg text-gray-600">Khám phá những bài viết hữu ích về thời trang, mẹo chăm sóc và xu hướng mới nhất</p>
    </div>

    <!-- Search & Filter Section -->
    <div class="mb-12 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Search Bar -->
        <form class="flex-1 w-full md:w-auto" method="get" action="index.php">
            <input type="hidden" name="action" value="blog_search">
            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                <i class="ri-search-line text-gray-400"></i>
                <input 
                    type="text" 
                    name="keyword" 
                    placeholder="Tìm kiếm bài viết..." 
                    class="bg-white border-none outline-none w-full px-2 py-2"
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="ri-search-line"></i>
                </button>
            </div>
        </form>

        <!-- Category Filter -->
        <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 w-full md:w-auto">
            <label class="text-[10px] font-bold text-gray-500 uppercase px-2">Danh mục:</label>
            <select onchange="window.location.href='index.php?action=blog_category&category=' + this.value"
                    class="bg-white border-none text-sm outline-none cursor-pointer font-medium p-1 rounded w-full md:w-auto">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" 
                            <?= (isset($_GET['category']) && $_GET['category'] === $cat) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Blogs Grid -->
    <?php if (empty($blogs)): ?>
        <div class="text-center py-20">
            <i class="ri-inbox-line text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-500">Không tìm thấy bài viết nào</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php foreach ($blogs as $blog): ?>
                <article class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all group">
                    <!-- Thumbnail -->
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        <?php if (!empty($blog['thumbnail'])): ?>
                            <img src="/web-shop-php/asset/<?= htmlspecialchars($blog['thumbnail']) ?>" 
                                 alt="<?= htmlspecialchars($blog['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                <i class="ri-article-line text-white text-5xl opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-3 left-3">
                            <span class="inline-block bg-black text-white text-xs font-bold px-3 py-1 rounded-full">
                                <?= htmlspecialchars($blog['category']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <!-- Meta -->
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                            <span><?= date('d/m/Y', strtotime($blog['created_at'])) ?></span>
                            <span>•</span>
                            <span><i class="ri-eye-line"></i> <?= number_format($blog['views']) ?> lượt xem</span>
                        </div>

                        <!-- Title -->
                        <h3 class="font-bold text-lg mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                            <a href="index.php?action=blog_detail&id=<?= $blog['blog_id'] ?>">
                                <?= htmlspecialchars($blog['title']) ?>
                            </a>
                        </h3>

                        <!-- Excerpt -->
                        <p class="text-sm text-gray-600 line-clamp-3 mb-4">
                            <?= htmlspecialchars($blog['excerpt'] ?? strip_tags($blog['content'])) ?>
                        </p>

                        <!-- Author -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    <?= substr($blog['author'] ?? 'A', 0, 1) ?>
                                </div>
                                <span class="text-xs font-medium"><?= htmlspecialchars($blog['author'] ?? 'Admin') ?></span>
                            </div>
                            <a href="index.php?action=blog_detail&id=<?= $blog['blog_id'] ?>" 
                               class="text-sm font-bold text-black hover:text-blue-600 transition-colors">
                                Đọc thêm <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center gap-3 mb-8">
                <?php 
                    $baseUrl = 'index.php?action=' . (isset($_GET['category']) ? 'blog_category&category=' . $_GET['category'] : (isset($_GET['keyword']) ? 'blog_search&keyword=' . $_GET['keyword'] : 'blogs'));
                ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= $baseUrl ?>&page=<?= $i ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-lg border font-bold transition-all
                       <?= $page === $i 
                           ? 'bg-black text-white border-black shadow-md' 
                           : 'bg-white text-gray-600 border-gray-200 hover:border-black' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<!-- Footer -->
<?php include_once PROJECT_ROOT . '/components/footer.php'; ?>

<script>
    // Thêm hiệu ứng smooth scroll
    document.querySelectorAll('a[href^="index.php"]').forEach(link => {
        link.addEventListener('click', function(e) {
            // Cho phép navigation thông thường
        });
    });
</script>
