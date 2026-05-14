<?php
$type = $type ?? 'best_sellers';
$products = $products ?? [];
$stats = $stats ?? [];
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ri-shopping-bag-line text-green-600"></i> Báo Cáo Sản Phẩm
        </h1>
        <p class="text-gray-600 mt-2">Thống kê bán chạy, tồn kho, doanh thu</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Tổng Sản Phẩm</p>
            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $stats['total_products'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Tổng Tồn Kho</p>
            <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_stock'] ?? 0) ?> sản phẩm</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 font-medium">Cảnh Báo Tồn Kho Thấp</p>
            <p class="text-3xl font-bold text-red-600 mt-2"><?= $stats['low_stock_count'] ?? 0 ?> sản phẩm</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex gap-2 flex-wrap">
            <a href="index.php?action=product_report&type=best_sellers" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $type === 'best_sellers' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                <i class="ri-fire-line"></i> Bán Chạy
            </a>
            <a href="index.php?action=product_report&type=top_revenue" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $type === 'top_revenue' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' ?>">
                <i class="ri-money-dollar-circle-line"></i> Doanh Thu Cao
            </a>
            <a href="index.php?action=product_report&type=low_stock" 
               class="px-4 py-2 rounded-lg border font-medium transition-all <?= $type === 'low_stock' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:border-red-600' ?>">
                <i class="ri-alert-line"></i> Tồn Kho Thấp
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">
                <?= $type === 'best_sellers' ? 'Sản Phẩm Bán Chạy' : ($type === 'low_stock' ? 'Sản Phẩm Tồn Kho Thấp' : 'Sản Phẩm Doanh Thu Cao') ?>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">Sản Phẩm</th>
                        <th class="px-6 py-4">Giá</th>
                        <?php if ($type !== 'low_stock'): ?>
                            <th class="px-6 py-4">Số Lượng Bán</th>
                            <th class="px-6 py-4">Doanh Thu</th>
                        <?php else: ?>
                            <th class="px-6 py-4">Tồn Kho Hiện Tại</th>
                            <th class="px-6 py-4">Mức Tối Thiểu</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($product['thumbnail'])): ?>
                                            <img src="/web-shop-php/asset/<?= htmlspecialchars($product['thumbnail']) ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                                 class="w-10 h-10 rounded object-cover">
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></p>
                                            <p class="text-xs text-gray-500">ID: <?= $product['product_id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <?= number_format($product['price'] ?? 0, 0, ',', '.') ?>₫
                                </td>
                                <?php if ($type !== 'low_stock'): ?>
                                    <td class="px-6 py-4 text-center font-bold text-blue-600">
                                        <?= $product['total_sold'] ?? 0 ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        <?= number_format($product['revenue'] ?? 0, 0, ',', '.') ?>₫
                                    </td>
                                <?php else: ?>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-bold 
                                            <?= ($product['stock_quantity'] ?? 0) == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                            <?= $product['stock_quantity'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700"><?= $product['reorder_level'] ?? 0 ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/components/admin_footer.php'; ?>
