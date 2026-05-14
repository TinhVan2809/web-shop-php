<?php
// Initialize variables for static analysis
$blog = $blog ?? [];
$relatedBlogs = $relatedBlogs ?? [];
?>
<main class="container mx-auto px-4 md:px-7 py-20 mt-20">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
        <article class="lg:col-span-2">
            <!-- Thumbnail -->
            <?php if (!empty($blog['thumbnail'])): ?>
                <div class="w-full h-96 bg-gray-100 rounded-xl overflow-hidden mb-8">
                    <img src="/web-shop-php/asset/<?= htmlspecialchars($blog['thumbnail']) ?>" 
                         alt="<?= htmlspecialchars($blog['title']) ?>"
                         class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <!-- Title & Meta -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-4">
                    <span class="inline-block bg-black text-white px-3 py-1 rounded-full text-xs font-bold">
                        <?= htmlspecialchars($blog['category']) ?>
                    </span>
                    <span>•</span>
                    <span><?= date('d/m/Y', strtotime($blog['created_at'])) ?></span>
                    <span>•</span>
                    <span><i class="ri-eye-line"></i> <?= number_format($blog['views']) ?> lượt xem</span>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= htmlspecialchars($blog['title']) ?></h1>

                <!-- Author -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        <?= substr($blog['author'] ?? 'A', 0, 1) ?>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900"><?= htmlspecialchars($blog['author'] ?? 'Admin') ?></p>
                        <p class="text-sm text-gray-500">Tác giả</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="prose prose-sm md:prose-base max-w-none mb-8">
                <?= $blog['content'] ?>
            </div>

            <!-- Share Section -->
            <div class="py-8 border-t border-gray-200 border-b">
                <p class="font-bold mb-4">Chia sẻ bài viết:</p>
                <div class="flex gap-4">
                    <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                       target="_blank" 
                       class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                        <i class="ri-facebook-line text-xl"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                       target="_blank" 
                       class="w-12 h-12 bg-sky-400 text-white rounded-full flex items-center justify-center hover:bg-sky-500 transition-colors">
                        <i class="ri-twitter-x-line text-xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                       target="_blank" 
                       class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors">
                        <i class="ri-linkedin-box-line text-xl"></i>
                    </a>
                </div>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <!-- Related Posts -->
            <?php if (!empty($relatedBlogs)): ?>
                <div class="bg-gray-50 rounded-xl p-6 mb-8 sticky top-24">
                    <h3 class="text-xl font-bold mb-6">Bài viết liên quan</h3>
                    <div class="space-y-4">
                        <?php foreach ($relatedBlogs as $relatedBlog): ?>
                            <a href="index.php?action=blog_detail&id=<?= $relatedBlog['blog_id'] ?>" 
                               class="group block p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all">
                                <?php if (!empty($relatedBlog['thumbnail'])): ?>
                                    <div class="h-32 bg-gray-100 rounded-lg overflow-hidden mb-3">
                                        <img src="/web-shop-php/asset/<?= htmlspecialchars($relatedBlog['thumbnail']) ?>" 
                                             alt="<?= htmlspecialchars($relatedBlog['title']) ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    </div>
                                <?php endif; ?>
                                <h4 class="font-bold text-sm line-clamp-2 group-hover:text-blue-600 transition-colors mb-2">
                                    <?= htmlspecialchars($relatedBlog['title']) ?>
                                </h4>
                                <p class="text-xs text-gray-500">
                                    <?= date('d/m/Y', strtotime($relatedBlog['created_at'])) ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Newsletter -->
            <div class="bg-black text-white rounded-xl p-6">
                <h3 class="text-xl font-bold mb-2">Đăng ký Newsletter</h3>
                <p class="text-sm text-gray-300 mb-4">Nhận những bài viết mới nhất trực tiếp vào hộp thư của bạn</p>
                <form class="flex flex-col gap-3">
                    <input type="email" 
                           placeholder="Email của bạn" 
                           class="bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 outline-none focus:border-white transition-colors">
                    <button type="submit" class="bg-white text-black font-bold py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        Đăng ký
                    </button>
                </form>
            </div>
        </aside>
    </div>
</main>

<!-- Footer -->
<?php include_once PROJECT_ROOT . '/components/footer.php'; ?>

<style>
    /* Prose styling for blog content */
    .prose {
        font-size: 1rem;
        line-height: 1.75;
    }
    
    .prose p {
        margin-bottom: 1rem;
        color: #374151;
    }
    
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .prose h1 { font-size: 1.875rem; }
    .prose h2 { font-size: 1.5rem; }
    .prose h3 { font-size: 1.25rem; }
    
    .prose ul, .prose ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .prose li {
        margin-bottom: 0.5rem;
    }
    
    .prose strong {
        font-weight: 700;
        color: #111827;
    }
    
    .prose a {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .prose a:hover {
        color: #1d4ed8;
    }
    
    .prose blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        margin-left: 0;
        margin-bottom: 1rem;
        color: #6b7280;
        font-style: italic;
    }
    
    .prose img {
        max-width: 100%;
        height: auto;
        margin: 1rem 0;
        border-radius: 0.5rem;
    }
</style>
