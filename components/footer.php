<footer class="w-full flex flex-col md:px-20 md:py-10">
    <div class="grid md:grid-cols-2 w-full justify-center md:justify-between gap-10">
        <div class="flex items-center">
            <img src="../asset/wf.png" class="w-30">
            <div class="flex justify-center items-center border border-stone-500/50 h-fit p-1 rounded-[23px] gap-2">
                <input type="text" placeholder="Email" class="w-40 outline-0 px-3">
                <button class="bg-black text-white px-4 py-2 rounded-[20px]">Subscribe</button>
            </div>
        </div>
        <nav>
            <ul class="grid grid-cols-4 gap-5 md:gap-0">
                <li class="flex flex-col gap-2">
                    <p class="font-[500]">Homespace</p>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Products</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Blogs</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Contact</a>
                </li>
                <li class="flex flex-col gap-2">
                    <p class="font-[500]">Studio</p>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Signature works</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">About</a>
                </li>
                <li class="flex flex-col gap-2">
                    <p class="font-[500]">Products</p>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Shirt</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Shoes</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Bags</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">pants</a>
                </li>
                <li class="flex flex-col gap-2">
                    <p class="font-[500]">Connect</p>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Instagram</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">X</a>
                    <a class="text-sm md:text-md hover:underline underline-ofset-1">Facebook</a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="flex mt-10 flex-col gap-5 justify-center items-center">
        <p class="logo-name text-[50px] md:text-[80px]">HASEKI STORE OFFICIAL</p>
        <p>&copy; 2026 . All right Reserves</p>
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

    // Xử lý chọn biến thể
    const variantOptions = document.querySelectorAll('.variant-option');
    variantOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            // Xóa highlight cũ
            variantOptions.forEach(el => el.classList.remove('border-black', 'ring-2', 'ring-black'));
            // Thêm highlight mới
            this.classList.add('border-black', 'ring-2', 'ring-black');

            // Cập nhật variant_id vào nút Add to Cart
            const variantId = this.getAttribute('data-variant-id');
            const btnAddToCart = document.querySelector('.btn-add-to-cart');
            if (btnAddToCart) {
                btnAddToCart.setAttribute('data-variant-id', variantId);
            }

            // Cập nhật ảnh chính nếu biến thể có ảnh riêng
            const variantImg = this.getAttribute('data-image');
            if (variantImg) {
                const mainImg = document.getElementById('main-image');
                if (mainImg) mainImg.src = variantImg;
            }
        });
    });

    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let productId = this.getAttribute('data-id');
            let variantId = this.getAttribute('data-variant-id') || '';
            // Check if there is a quantity input for details page
            let quantityInput = document.getElementById('product-quantity');
            let quantity = quantityInput ? quantityInput.value : 1;

            fetch('index.php?action=add_to_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}&variant_id=${variantId}`
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