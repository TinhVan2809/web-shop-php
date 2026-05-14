<?php
// $categories: associative array keyed by category_name ('shoes','bags','shirt','pants')
?>
<main class="container mx-auto px-7 py-10 mt-30">
    <h1 class="text-3xl font-bold mb-6">Sản phẩm theo danh mục</h1>

    <?php foreach ($categories as $key => $cat): ?>
        <section class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold"><?php echo $cat['title']; ?></h2>
                <?php if (!empty($cat['category_id'])): ?>
                    <a href="index.php?action=category&id=<?php echo $cat['category_id']; ?>" class="text-sm text-blue-600">Xem tất cả</a>
                <?php endif; ?>
            </div>

            <?php if (empty($cat['products'])): ?>
                <p class="text-gray-500">Không có sản phẩm trong danh mục này.</p>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <?php foreach ($cat['products'] as $product): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow bg-white p-3">
                            <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>" class="block">
                                <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden mb-3">
                                    <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                                </div>
                                <h3 class="font-semibold text-sm mb-1 truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-red-600 font-bold"><?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫</p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center justify-center mt-4 gap-2">
                    <?php
                        $pageParam = 'page_' . $key;
                        $current = $cat['current_page'];
                        $total = $cat['total_pages'];
                        $qs = $_GET;
                    ?>
                    <?php if ($current > 1): ?>
                        <?php $qs[$pageParam] = $current - 1; ?>
                        <a href="index.php?action=products&<?php echo http_build_query($qs); ?>" class="px-3 py-1 border rounded">&laquo; Trước</a>
                    <?php endif; ?>

                    <span class="px-3 py-1 border rounded bg-gray-100"><?php echo $current; ?> / <?php echo $total; ?></span>

                    <?php if ($current < $total): ?>
                        <?php $qs[$pageParam] = $current + 1; ?>
                        <a href="index.php?action=products&<?php echo http_build_query($qs); ?>" class="px-3 py-1 border rounded">Tiếp &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</main>
