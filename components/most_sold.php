<?php

class Most_sold
{

    public function mostSold()
    {
        $database = new Database();
        $db = $database->getConnection();

        // Truy vấn lấy đầy đủ thông tin sản phẩm bán chạy nhất
        $query = "SELECT p.*, c.category_name, SUM(oi.quantity) AS total_sold
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.product_id 
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.status = 'active'
                  GROUP BY p.product_id
                  ORDER BY total_sold DESC 
                  LIMIT 3";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($products): ?>
            <section class="hidden md:block container mx-auto w-full mt-10 p-10">

            <div class="mb-10">
               <div class="flex justify-center items-center gap-6">
                 <hr class="border border-black w-125">
                <div class="">
                    <h1 class="text-xl font-[550]">Best sellers</h1>
                    <a href="index.php?action=best_sellers" class="text-sm">View all <i class="ri-arrow-right-line"></i></a>
                </div>
                <hr class="border border-black w-125">
               </div>
            </div>
                <div class="grid grid-cols-5 w-full gap-x-5">
                    <div class="col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($products as $product): ?>
                        <div class="group relative flex flex-col">
                            <div class="relative aspect-[3/4] overflow-hidden rounded-xl bg-gray-100 mb-4">
                                <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                
                                <!-- Badge số lượng đã bán -->
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm">
                                    <p class="text-[10px] font-bold uppercase tracking-tight text-gray-700">
                                        Đã bán <?php echo $product['total_sold']; ?>
                                    </p>
                                </div>

                                <button class="absolute bottom-4 right-4 bg-black text-white p-3 rounded-full shadow-xl opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 btn-add-to-cart" 
                                        data-id="<?php echo $product['product_id']; ?>">
                                    <i class="ri-shopping-bag-line text-xl"></i>
                                </button>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest"><?php echo $product['category_name']; ?></span>
                                <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>">
                                    <h3 class="font-bold text-gray-900 hover:text-blue-600 transition-colors truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="font-bold text-lg"><?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫</span>
                                    <?php if ($product['discount_price']): ?>
                                        <span class="text-sm text-gray-400 line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-span-2">
                    <img src="../asset/banner-mostsold.jpg">
                </div>
                </div>
            </section>
        <?php endif;
    }
}
  
