<main class="container mx-auto px-7 py-10 mt-20 min-h-[60vh]">
    <h1 class="text-3xl font-bold mb-8">Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-20 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
            <i class="ri-shopping-cart-2-line text-6xl text-gray-300 mb-4 block"></i>
            <p class="text-xl text-gray-500 mb-6">Giỏ hàng của bạn đang trống</p>
            <a href="index.php" class="bg-black text-white px-8 py-3 rounded-full font-bold hover:bg-gray-800 transition-colors inline-block">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 hidden md:table-row">
                                <th class="p-4 font-medium">Sản phẩm</th>
                                <th class="p-4 font-medium">Đơn giá</th>
                                <th class="p-4 font-medium text-center">Số lượng</th>
                                <th class="p-4 font-medium text-right">Thành tiền</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <?php $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']); ?>
                                <tr class="border-b border-gray-100 last:border-0 flex flex-col md:table-row relative" 
                                    data-id="<?php echo $item['product_id']; ?>" 
                                    data-variant-id="<?php echo $item['variant_id'] ?? ''; ?>">
                                    <td class="p-4">
                                        <div class="flex items-center gap-4">
                                            <img src="/web-shop-php/asset/<?php echo htmlspecialchars($item['thumbnail']); ?>" class="w-24 h-24 object-cover rounded-lg border border-gray-100">
                                            <div class="flex flex-col">
                                                <a href="index.php?action=detail&id=<?php echo $item['product_id']; ?>" class="font-medium hover:text-blue-600 text-lg line-clamp-2 pr-8 md:pr-0"><?php echo htmlspecialchars($item['name']); ?></a>
                                                <?php if (!empty($item['variant_details'])): ?>
                                                    <p class="text-xs text-gray-500 mt-1 italic">Phân loại: <?php echo htmlspecialchars($item['variant_details']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-red-600 font-bold md:table-cell hidden">
                                        <?php echo number_format($price, 0, ',', '.'); ?>₫
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center md:justify-center border border-gray-300 rounded-lg w-32 md:mx-auto">
                                            <button class="px-3 py-1 hover:bg-gray-100 btn-decrease text-xl" data-id="<?php echo $item['product_id']; ?>" data-variant-id="<?php echo $item['variant_id'] ?? ''; ?>">-</button>
                                            <input type="number" class="w-12 text-center outline-none quantity-input font-medium" value="<?php echo $item['quantity']; ?>" 
                                                   data-id="<?php echo $item['product_id']; ?>" data-variant-id="<?php echo $item['variant_id'] ?? ''; ?>" min="1">
                                            <button class="px-3 py-1 hover:bg-gray-100 btn-increase text-xl" data-id="<?php echo $item['product_id']; ?>" data-variant-id="<?php echo $item['variant_id'] ?? ''; ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-800 item-total md:table-cell hidden" data-price="<?php echo $price; ?>">
                                        <?php echo number_format($price * $item['quantity'], 0, ',', '.'); ?>₫
                                    </td>
                                    <td class="p-4 text-center absolute top-2 right-2 md:relative md:top-0 md:right-0">
                                        <button class="text-gray-400 hover:text-red-500 transition-colors btn-remove p-2 bg-gray-50 md:bg-transparent rounded-full" 
                                                data-id="<?php echo $item['product_id']; ?>" data-variant-id="<?php echo $item['variant_id'] ?? ''; ?>">
                                            <i class="ri-delete-bin-line text-xl"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 sticky top-24">
                    <h2 class="text-xl font-bold mb-6 pb-4 border-b border-gray-200">Tổng giỏ hàng</h2>
                    <div class="space-y-4 text-gray-600 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between">
                            <span>Tạm tính</span>
                            <span class="font-medium" id="summary-subtotal"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Thuế (10%)</span>
                            <span class="font-medium" id="summary-tax"><?php echo number_format($tax, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-medium" id="summary-shipping"><?php echo number_format($shipping, 0, ',', '.'); ?>₫</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-8">
                        <span class="font-bold text-lg">Tổng cộng</span>
                        <span class="font-bold text-3xl text-red-600" id="summary-total"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php?action=checkout" class="w-full bg-black text-white py-4 rounded-full font-bold hover:bg-gray-800 transition-colors text-lg shadow-lg flex justify-center items-center gap-2">
                            Tiến hành thanh toán <i class="ri-arrow-right-line"></i>
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=login&redirect=checkout" class="w-full bg-black text-white py-4 rounded-full font-bold hover:bg-gray-800 transition-colors text-lg shadow-lg flex justify-center items-center gap-2 text-center">
                            Đăng nhập để thanh toán <i class="ri-arrow-right-line"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '₫';
    }

    function updateCart(productId, quantity, variantId) {
        fetch('index.php?action=update_cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}&variant_id=${variantId}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Update summary
                document.getElementById('summary-subtotal').textContent = data.subtotal + '₫';
                document.getElementById('summary-tax').textContent = data.tax + '₫';
                document.getElementById('summary-shipping').textContent = data.shipping + '₫';
                document.getElementById('summary-total').textContent = data.total + '₫';
                
                // Update cart count badge if exists
                let cartBadge = document.getElementById('cart-count-badge');
                if(cartBadge && data.cartCount !== undefined) {
                    cartBadge.textContent = data.cartCount;
                    cartBadge.style.display = data.cartCount > 0 ? 'flex' : 'none';
                }
            }
        });
    }

    function removeCartItem(productId, variantId, rowElement) {
        fetch('index.php?action=remove_from_cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&variant_id=${variantId}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                rowElement.remove();
                
                // Update summary
                document.getElementById('summary-subtotal').textContent = data.subtotal + '₫';
                document.getElementById('summary-tax').textContent = data.tax + '₫';
                document.getElementById('summary-shipping').textContent = data.shipping + '₫';
                document.getElementById('summary-total').textContent = data.total + '₫';
                
                // Update cart count badge
                let cartBadge = document.getElementById('cart-count-badge');
                if(cartBadge && data.cartCount !== undefined) {
                    cartBadge.textContent = data.cartCount;
                    cartBadge.style.display = data.cartCount > 0 ? 'flex' : 'none';
                }

                // If cart is empty, reload page to show empty state
                if (data.cartCount === 0) {
                    window.location.reload();
                }
            }
        });
    }

    // Event listeners for quantity changes
    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let vid = this.getAttribute('data-variant-id');
            let input = document.querySelector(`input.quantity-input[data-id="${id}"][data-variant-id="${vid}"]`);
            let row = document.querySelector(`tr[data-id="${id}"][data-variant-id="${vid}"]`);
            let totalEl = row.querySelector('.item-total');
            let price = parseInt(totalEl.getAttribute('data-price'));
            
            let val = parseInt(input.value);
            if (val > 1) {
                input.value = val - 1;
                totalEl.textContent = formatCurrency(price * (val - 1));
                updateCart(id, val - 1, vid);
            }
        });
    });

    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let vid = this.getAttribute('data-variant-id');
            let input = document.querySelector(`input.quantity-input[data-id="${id}"][data-variant-id="${vid}"]`);
            let row = document.querySelector(`tr[data-id="${id}"][data-variant-id="${vid}"]`);
            let totalEl = row.querySelector('.item-total');
            let price = parseInt(totalEl.getAttribute('data-price'));
            
            let val = parseInt(input.value);
            input.value = val + 1;
            totalEl.textContent = formatCurrency(price * (val + 1));
            updateCart(id, val + 1, vid);
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            let id = this.getAttribute('data-id');
            let vid = this.getAttribute('data-variant-id');
            let row = document.querySelector(`tr[data-id="${id}"][data-variant-id="${vid}"]`);
            let totalEl = row.querySelector('.item-total');
            let price = parseInt(totalEl.getAttribute('data-price'));
            
            let val = parseInt(this.value);
            if (val < 1 || isNaN(val)) {
                val = 1;
                this.value = 1;
            }
            totalEl.textContent = formatCurrency(price * val);
            updateCart(id, val, vid);
        });
    });

    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let vid = this.getAttribute('data-variant-id');
            let row = document.querySelector(`tr[data-id="${id}"][data-variant-id="${vid}"]`);
            removeCartItem(id, vid, row);
        });
    });
});
</script>
