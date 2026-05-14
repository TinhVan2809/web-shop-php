<?php
$page = $page ?? [];
?>

<main class="container mx-auto px-4 md:px-7 py-20 mt-20">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-gray-600 text-sm mb-8">
        <a href="index.php" class="hover:text-gray-900">Trang Chủ</a>
        <span>→</span>
        <span class="text-gray-900 font-semibold"><?= htmlspecialchars($page['title']) ?></span>
    </div>

    <!-- Page Title -->
    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
        <?= htmlspecialchars($page['title']) ?>
    </h1>

    <!-- Page Meta -->
    <div class="text-sm text-gray-500 mb-12 pb-12 border-b border-gray-200">
        <p>Cập nhật lần cuối: <?= date('d/m/Y', strtotime($page['updated_at'])) ?></p>
    </div>

    <!-- Page Content -->
    <div class="prose prose-lg max-w-full mb-12">
        <?= $page['content'] ?>
    </div>

    <!-- Back to Home -->
    <div class="mt-12 pt-12 border-t border-gray-200">
        <a href="index.php" class="inline-block text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-2">
            <i class="ri-arrow-left-line"></i> Quay lại trang chủ
        </a>
    </div>
</main>

<style>
    .prose {
        font-size: 1.05rem;
        line-height: 1.8;
    }
    
    .prose p {
        margin-bottom: 1.25rem;
        color: #374151;
    }
    
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #111827;
    }
    
    .prose h1 { font-size: 2.25rem; }
    .prose h2 { font-size: 1.875rem; }
    .prose h3 { font-size: 1.5rem; }
    
    .prose ul, .prose ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .prose li {
        margin-bottom: 0.5rem;
    }
    
    .prose a {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .prose a:hover {
        color: #1d4ed8;
    }
    
    .prose strong {
        font-weight: 700;
        color: #111827;
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
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .prose code {
        background-color: #f3f4f6;
        color: #d97706;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-family: monospace;
    }
    
    .prose pre {
        background-color: #1f2937;
        color: #f3f4f6;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    
    .prose table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    
    .prose table th, .prose table td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        text-align: left;
    }
    
    .prose table th {
        background-color: #f3f4f6;
        font-weight: 600;
    }
</style>

<?php include_once PROJECT_ROOT . '/components/footer.php'; ?>
