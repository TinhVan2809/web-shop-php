<main class="container mx-auto px-7 py-20 mt-20">
    <div class="mb-12 border-b border-gray-100 pb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        
        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            <!-- Bộ lọc danh mục -->
            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 flex-1 md:flex-none">
                <label class="text-[10px] font-bold text-gray-500 uppercase px-2">Danh mục:</label>
                <select onchange="updateFilters('category_id', this.value)" 
                        class="bg-white border-none text-sm outline-none cursor-pointer font-medium p-1 rounded w-full">
                    <option value="all">Tất cả</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= (isset($category_id) && $category_id == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Bộ lọc khoảng giá -->
            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 flex-1 md:flex-none">
                <label class="text-[10px] font-bold text-gray-500 uppercase px-2">Khoảng giá:</label>
                <select onchange="updateFilters('price_range', this.value)" 
                        class="bg-white border-none text-sm outline-none cursor-pointer font-medium p-1 rounded w-full">
                    <option value="all" <?= $price_range === 'all' ? 'selected' : '' ?>>Tất cả giá</option>
                    <option value="under-500" <?= $price_range === 'under-500' ? 'selected' : '' ?>>Dưới 500k</option>
                    <option value="500-2000" <?= $price_range === '500-2000' ? 'selected' : '' ?>>500k - 2tr</option>
                    <option value="over-2000" <?= $price_range === 'over-2000' ? 'selected' : '' ?>>Trên 2tr</option>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php if (empty($products)): ?>
            <div class="col-span-full text-center py-20">
                <p class="text-gray-400 italic">Hiện chưa có dữ liệu bán hàng.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                    <div class="block relative h-72 bg-gray-100 overflow-hidden">
                        <a href="index.php?action=detail&id=<?= $product['product_id'] ?>">
                            <img src="/web-shop-php/asset/<?= $product['thumbnail'] ?>" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </a>
                        
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm z-10">
                            <p class="text-[10px] font-bold uppercase tracking-tight text-gray-700">
                                <i class="ri-fire-fill text-orange-500"></i> Đã bán <?= $product['total_sold'] ?>
                            </p>
                        </div>

                        <button class="absolute top-2 right-2 p-3 btn-toggle-favorite z-20" data-id="<?= $product['product_id'] ?>">
                            <i class="<?= !empty($product['is_favorited']) ? 'ri-heart-3-fill text-red-500' : 'ri-heart-3-line' ?> text-2xl hover:text-red-500 transition-colors"></i>
                        </button>

                        <div class="p-4 bg-white/90 absolute bottom-0 w-full translate-y-full group-hover:translate-y-0 transition-transform flex justify-between items-center">
                            <div class="overflow-hidden">
                                <h3 class="font-bold text-sm truncate"><?= htmlspecialchars($product['name']) ?></h3>
                                <p class="text-red-600 font-bold"><?= number_format($product['discount_price'] ?? $product['price'], 0, ',', '.') ?>₫</p>
                            </div>
                            <button class="bg-black text-white p-2 rounded-full btn-add-to-cart" data-id="<?= $product['product_id'] ?>">
                                <i class="ri-shopping-cart-2-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-16 flex justify-center gap-3">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php 
                    $url = "index.php?action=best_sellers&page=$i";
                    if (isset($category_id)) {
                        $url .= "&category_id=$category_id";
                    }
                    if (isset($price_range) && $price_range !== 'all') {
                        $url .= "&price_range=$price_range";
                    }
                ?>
                <a href="<?= $url ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-lg border font-bold transition-all
                   <?= $page === $i 
                       ? 'bg-black text-white border-black shadow-md' 
                       : 'bg-white text-gray-600 border-gray-200 hover:border-black' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    function updateFilters(key, value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set(key, value);
        urlParams.set('page', '1'); // Reset về trang 1 khi lọc
        window.location.href = 'index.php?' + urlParams.toString();
    }
</script>
