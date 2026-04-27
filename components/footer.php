<footer class="grid md:grid-cols-3 bg-[#36522e] text-[#f4bb3e] justify-center gap-10 p-10">
    <div class="flex justify-center">
        <img src="../asset/footer1.jpg" class="w-65">
    </div>
    <div class="flex flex-col gap-3 items-start">
        <h2 class="text-2xl font-blod">Issue: Products</h2>
        <span class="text-sm">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis earum culpa eum officia. Blanditiis vel tempore provident cum consequuntur repudiandae veritatis numquam nam ab fuga.</span>
        <button class="bg-[#f4bb3e] text-black px-3 py-1 font-blod rounded-[20px]">Shop Now</button>
    </div>
    <div class="flex flex-col gap-1">
        <p class="mb-3">Find dhloop stocked at</p>
        <a href="#" class="text-sm hover:underline">The Farm, Chemanai</a>
        <a href="#" class="text-sm hover:underline">G5A, Mumbai</a>
        <a href="#" class="text-sm hover:underline">Omnivore, Bookstore, San Fransisco</a>
        <a href="#" class="text-sm hover:underline">McNally, Jackson, Books, New York City</a>
        <a href="#" class="text-sm hover:underline">View All Stockist</a>
    </div>
</footer>

<!-- Toast Notification -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `px-6 py-3 rounded-lg text-white shadow-lg transform transition-all duration-300 translate-y-10 opacity-0 flex items-center gap-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        
        const icon = type === 'success' ? '<i class="ri-check-line text-xl"></i>' : '<i class="ri-error-warning-line text-xl"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        
        document.getElementById('toast-container').appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);
        
        // Remove after 3s
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let productId = this.getAttribute('data-id');
            // Check if there is a quantity input for details page
            let quantityInput = document.getElementById('product-quantity');
            let quantity = quantityInput ? quantityInput.value : 1;

            fetch('index.php?action=add_to_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast(data.message, 'success');
                    
                    // Update cart count badge
                    let cartBadge = document.getElementById('cart-count-badge');
                    if(cartBadge && data.cartCount !== undefined) {
                        cartBadge.textContent = data.cartCount;
                        cartBadge.style.display = data.cartCount > 0 ? 'flex' : 'none';
                        
                        // Small animation on badge
                        cartBadge.classList.add('scale-150', 'transition-transform');
                        setTimeout(() => cartBadge.classList.remove('scale-150'), 200);
                    }
                } else {
                    showToast('Có lỗi xảy ra, vui lòng thử lại!', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Không thể kết nối đến máy chủ!', 'error');
            });
        });
    });

    // Xử lý Favorite Toggle bằng Event Delegation
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-toggle-favorite');
        if (!btn) return;

        // Ngăn chặn chuyển trang nếu nút nằm trong thẻ <a>
        e.preventDefault();
        e.stopPropagation();

        const productId = btn.getAttribute('data-id');
        const icon = btn.querySelector('i');

        fetch('index.php?action=toggle_favorite', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (data.isFavorited) {
                    icon.className = icon.className.replace('-line', '-fill');
                    icon.classList.add('text-red-500');
                } else {
                    icon.className = icon.className.replace('-fill', '-line');
                    icon.classList.remove('text-red-500');
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Không thể kết nối đến máy chủ!', 'error');
        });
    });
});
</script>