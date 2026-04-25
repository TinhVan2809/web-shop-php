<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold">Danh sách sản phẩm</h2>
        <p class="text-gray-500 text-sm mt-1">Quản lý kho hàng và trạng thái kinh doanh</p>
    </div>
    <a href="index.php?action=product_form" class="bg-black text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 hover:bg-gray-800 transition-all">
        <i class="ri-add-line"></i>
        Thêm sản phẩm
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Danh mục</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Giá bán</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($products as $product): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="/web-shop-php/asset/<?= $product['thumbnail'] ?>" class="w-12 h-12 rounded-lg object-cover bg-gray-50">
                            <div>
                                <p class="font-semibold text-gray-900"><?= $product['name'] ?></p>
                                <p class="text-xs text-gray-500">SKU: <?= $product['sku'] ?: 'N/A' ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?= $product['category_name'] ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-900"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
                        <?php if($product['discount_price']): ?>
                            <p class="text-xs text-red-500 line-through"><?= number_format($product['discount_price'], 0, ',', '.') ?>₫</p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $statusMap = [
                            'active' => ['bg-green-50 text-green-600', 'ĐANG BÁN'],
                            'inactive' => ['bg-gray-50 text-gray-600', 'NGỪNG BÁN'],
                            'out_of_stock' => ['bg-red-50 text-red-600', 'HẾT HÀNG']
                        ];
                        $st = $statusMap[$product['status']] ?? ['bg-gray-50 text-gray-600', 'KHOÔNG XÁC ĐỊNH'];
                        ?>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $st[0] ?>">
                            <?= $st[1] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="index.php?action=product_form&id=<?= $product['product_id'] ?>" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="index.php?action=delete_product&id=<?= $product['product_id'] ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')"
                               class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
