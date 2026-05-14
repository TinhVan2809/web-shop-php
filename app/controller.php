<?php

require_once PROJECT_ROOT . '/app/Database.php';

class Controller
{
    public function index()
    {
        $database = new Database();
        $db = $database->getConnection();

        // Truy vấn lấy sản phẩm kèm tên danh mục
        $user_id = $_SESSION['user_id'] ?? 0;

        // Truy vấn lấy sản phẩm kèm tên danh mục và trạng thái yêu thích
        $query = "SELECT p.*, c.category_name, f.farority_id AS is_favorited
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  LEFT JOIN favority f ON p.product_id = f.product_id AND f.user_id = :user_id
                  WHERE p.status = 'active' 
                  ORDER BY p.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Truy vấn lấy tất cả banner từ database
        $bannerQuery = "SELECT * FROM banner ORDER BY create_at DESC";
        $bannerStmt = $db->prepare($bannerQuery);
        $bannerStmt->execute();
        $banners = $bannerStmt->fetchAll(PDO::FETCH_ASSOC);

        // Include header (đã có sẵn HTML, Head, Body mở)
        include_once PROJECT_ROOT . '/components/header.php';

        // Include banner 
        include_once PROJECT_ROOT . '/components/banner.php';

        // Include Danh mục
        include_once PROJECT_ROOT . '/components/categories.php';

        // Include introducing
        include_once PROJECT_ROOT . '/components/introducing.php';

        // Include most sold
        require_once PROJECT_ROOT . '/components/most_sold.php';
        (new Most_sold())->mostSold();

        // Include most favority
        require_once PROJECT_ROOT . '/components/most_favority.php';
        (new most_favority())->mostFavority();
?>

        <?php
        // Include ads
        include_once PROJECT_ROOT . '/components/ads.php';


        // Include logo animation
        include_once PROJECT_ROOT . '/components/logo_manu.php';


        // Footer
        include_once PROJECT_ROOT . '/components/footer.php';
    }


    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        $user_id = $_SESSION['user_id'] ?? 0;

        $query = "SELECT p.*, c.category_name, m.manufacturer_name, m.logo_img, f.farority_id AS is_favorited, i.available_quantity	
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                  LEFT JOIN inventory i ON i.product_id = p.product_id
                  LEFT JOIN favority f ON p.product_id = f.product_id AND f.user_id = :user_id
                  WHERE p.product_id = :id";

        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách ảnh phụ từ bảng product_images
        $imgQuery = "SELECT image FROM product_images WHERE product_id = :id";
        $imgStmt = $db->prepare($imgQuery);
        $imgStmt->execute(['id' => $id]);
        $extra_images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách biến thể và thuộc tính của từng biến thể
        $variantQuery = "SELECT pv.*, va.attribute_name, va.attribute_value, i.available_quantity 
                        FROM product_variants pv 
                        LEFT JOIN variant_attributes va ON pv.variant_id = va.variant_id 
                        LEFT JOIN inventory i ON pv.variant_id = i.variant_id
                        WHERE pv.product_id = :id";
        $vStmt = $db->prepare($variantQuery);
        $vStmt->execute(['id' => $id]);
        $rawVariants = $vStmt->fetchAll(PDO::FETCH_ASSOC);

        $variants = [];
        foreach ($rawVariants as $row) {
            $vid = $row['variant_id'];
            if (!isset($variants[$vid])) {
                $variants[$vid] = $row;
                $variants[$vid]['attrs'] = [];
            }
            if ($row['attribute_name']) {
                $variants[$vid]['attrs'][$row['attribute_name']] = $row['attribute_value'];
            }
        }

        // Build attribute options map for UI (e.g. color => ["red" => [vids], ...])
        $attrOptions = [];
        foreach ($variants as $v) {
            if (!empty($v['attrs'])) {
                foreach ($v['attrs'] as $name => $value) {
                    if (!isset($attrOptions[$name])) $attrOptions[$name] = [];
                    if (!isset($attrOptions[$name][$value])) $attrOptions[$name][$value] = [];
                    $attrOptions[$name][$value][] = $v['variant_id'];
                }
            }
        }

        // Lấy danh sách đánh giá kèm thông tin người dùng
        $ratingFilter = $_GET['rating_filter'] ?? 'all';
        $reviewParams = ['id' => $id];
        $reviewQuery = "SELECT r.*, u.name as user_name, u.avatar 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.user_id 
                        WHERE r.product_id = :id";

        if ($ratingFilter !== 'all') {
            $reviewQuery .= " AND r.rating = :rating";
            $reviewParams['rating'] = (int)$ratingFilter;
        }

        $reviewQuery .= " ORDER BY r.review_id DESC";
        $rStmt = $db->prepare($reviewQuery);
        $rStmt->execute($reviewParams);
        $reviews = $rStmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy trung bình số sao và tổng số đánh giá từ database
        $avgRatingQuery = "SELECT AVG(rating) as avg_rating, COUNT(review_id) as total_reviews FROM reviews WHERE product_id = :id";
        $avgStmt = $db->prepare($avgRatingQuery);
        $avgStmt->execute(['id' => $id]);
        $ratingData = $avgStmt->fetch(PDO::FETCH_ASSOC);
        $avgRating = round($ratingData['avg_rating'] ?? 0, 1);
        $totalReviews = $ratingData['total_reviews'] ?? 0;

        include_once PROJECT_ROOT . '/components/header.php';

        if (!$product): ?>
            <div class="container mx-auto px-7 py-20 text-center">
                <h1 class="text-2xl font-bold mb-4">Sản phẩm không tồn tại!</h1>
                <a href="index.php" class="text-blue-600 hover:underline">Quay lại trang chủ</a>
            </div>
        <?php else: ?>
            <main class="container mx-auto px-7 py-10 mt-30">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <img id="main-image" src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" alt="<?php echo $product['name']; ?>" class="w-full rounded-2xl transition-all duration-300">

                        <?php if (!empty($extra_images)): ?>
                            <div class="grid grid-cols-4 gap-4 mt-4">
                                <!-- Hiển thị thumbnail chính như một phần của gallery -->
                                <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>"
                                    class="w-full aspect-square object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-black transition-all"
                                    onclick="document.getElementById('main-image').src=this.src">

                                <?php foreach ($extra_images as $img): ?>
                                    <img src="/web-shop-php/asset/<?php echo $img['image']; ?>"
                                        class="w-full aspect-square object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-black transition-all"
                                        onclick="document.getElementById('main-image').src=this.src">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold mb-2"><?php echo $product['name']; ?></h1>
                        <p class="text-gray-500 mb-4 uppercase tracking-wider text-sm"><?php echo $product['category_name']; ?> | <?php echo $product['manufacturer_name'] ?? 'Haseki Store'; ?></p>

                        <!-- Hiển thị đánh giá trung bình -->
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="ri-star-<?php echo ($i <= round($avgRating)) ? 'fill' : 'line'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm font-bold text-gray-700"><?php echo $avgRating; ?>/5</span>
                            <span class="text-sm text-gray-500">(<?php echo $totalReviews; ?> đánh giá)</span>
                        </div>

                        <div class="text-3xl font-bold text-red-600 mb-6">
                            <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫
                            <?php if ($product['discount_price']): ?>
                                <span class="text-gray-400 text-xl line-through ml-3"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="prose max-w-none text-gray-700 mb-8 leading-relaxed">
                            <?php echo nl2br($product['description'] ?: $product['short_description']); ?>
                        </div>

                        <?php if (!empty($variants)): ?>
                            <div class="mb-8">
                                <h3 class="text-sm font-bold text-gray-900 uppercase mb-4">Tùy chọn sản phẩm:</h3>
                                <div class="flex flex-wrap gap-3" id="variant-container">
                                    <?php foreach ($variants as $v): ?>
                                        <div class="variant-option border rounded-xl p-3 cursor-pointer hover:border-black transition-all group relative"
                                            data-variant-id="<?php echo $v['variant_id']; ?>"
                                            data-price="<?php echo $v['price'] ?: $product['discount_price'] ?? $product['price']; ?>"
                                            data-image="<?php echo $v['image'] ? '/web-shop-php/asset/' . $v['image'] : ''; ?>"
                                            data-stock="<?php echo $v['available_quantity'] ?? 0; ?>">

                                            <div class="text-xs font-bold text-gray-500 mb-1"><?php echo $v['sku']; ?></div>
                                            <div class="text-sm">
                                                <?php foreach ($v['attrs'] as $name => $val): ?>
                                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-[10px] mr-1">
                                                        <strong><?php echo $name; ?>:</strong> <?php echo $val; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if ($v['price']): ?>
                                                <div class="mt-1 text-sm font-bold text-blue-600"><?php echo number_format($v['price'], 0, ',', '.'); ?>₫</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center border border-gray-300 rounded-full px-4 py-2 w-32">
                                <button type="button" class="text-xl px-2 hover:text-red-500" onclick="document.getElementById('product-quantity').stepDown()">-</button>
                                <input type="number" id="product-quantity" value="1" min="1" class="w-full text-center outline-none bg-transparent font-medium">
                                <button type="button" class="text-xl px-2 hover:text-green-500" onclick="document.getElementById('product-quantity').stepUp()">+</button>
                            </div>
                            <button class="bg-black text-white px-10 py-4 rounded-full font-bold hover:bg-gray-800 transition-all btn-add-to-cart flex-1" data-id="<?php echo $product['product_id']; ?>" data-variant-id="">
                                THÊM VÀO GIỎ HÀNG
                            </button>
                            <button class="py-3 px-3.5 border border-gray-200 rounded-full hover:bg-gray-50 transition-colors btn-toggle-favorite" data-id="<?php echo $product['product_id']; ?>">
                                <i class="<?php echo !empty($product['is_favorited']) ? 'ri-heart-fill text-red-500' : 'ri-heart-line'; ?> text-2xl"></i>
                            </button>
                        </div>
                        <div class="">
                            <span>Tồn kho (<span id="stock-count"><?php echo $product['available_quantity'] ?? 0; ?></span>)</span>
                        </div>
                    </div>
                </div>

                <!-- Phần Đánh giá sản phẩm -->
                <div class="mt-20 border-t pt-10">
                    <h2 class="text-2xl font-bold mb-8">Đánh giá sản phẩm (<?php echo count($reviews); ?>)</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <!-- Danh sách đánh giá -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Bộ lọc đánh giá -->
                            <div class="flex flex-wrap gap-2 mb-8 items-center border-b pb-6">
                                <span class="text-sm font-bold text-gray-500 mr-2 uppercase tracking-wider">Lọc xem:</span>
                                <a href="index.php?action=detail&id=<?= $id ?>&rating_filter=all"
                                    data-rating="all"
                                    class="filter-review-btn px-4 py-1.5 rounded-full text-sm font-medium border <?= $ratingFilter === 'all' ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-black' ?> transition-all">
                                    Tất cả
                                </a>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <a href="index.php?action=detail&id=<?= $id ?>&rating_filter=<?= $i ?>"
                                        data-rating="<?= $i ?>"
                                        class="filter-review-btn px-4 py-1.5 rounded-full text-sm font-medium border <?= (string)$ratingFilter === (string)$i ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-black' ?> flex items-center gap-1 transition-all">
                                        <?= $i ?> <i class="ri-star-fill text-yellow-400"></i>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <div id="reviews-list-container" class="space-y-8">
                                <?php if (empty($reviews)): ?>
                                    <p class="text-gray-500 italic">Sản phẩm này chưa có đánh giá nào.</p>
                                <?php else: ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="flex gap-4 pb-6 border-b border-gray-100 last:border-0">
                                            <img src="/web-shop-php/asset/<?php echo $review['avatar'] ?: 'default_avatar.png'; ?>" class="w-12 h-12 rounded-full object-cover">
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center mb-1">
                                                    <h4 class="font-bold"><?php echo htmlspecialchars($review['user_name']); ?></h4>
                                                    <div class="flex text-yellow-400 text-sm">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="ri-star-<?php echo $i <= $review['rating'] ? 'fill' : 'line'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <p class="text-gray-600 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($review['content'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Form gửi đánh giá -->
                        <div class="bg-gray-50 p-6 rounded-2xl h-fit">
                            <h3 class="font-bold text-lg mb-4">Viết đánh giá của bạn</h3>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="index.php?action=add_review" method="POST" class="space-y-4">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn số sao:</label>
                                        <div class="flex gap-2 text-2xl text-gray-300" id="star-rating">
                                            <input type="hidden" name="rating" id="rating-value" value="5" required>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="ri-star-fill cursor-pointer transition-colors hover:text-yellow-400 star-btn text-yellow-400" data-value="<?php echo $i; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung đánh giá:</label>
                                        <textarea name="content" rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                                    </div>

                                    <button type="submit" class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all">GỬI ĐÁNH GIÁ</button>
                                </form>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500 mb-4">Bạn cần đăng nhập để viết đánh giá.</p>
                                    <a href="index.php?action=login&redirect=detail&id=<?php echo $product['product_id']; ?>" class="inline-block bg-black text-white px-6 py-2 rounded-full text-sm font-bold">Đăng nhập ngay</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const stars = document.querySelectorAll('.star-btn');
                        const ratingInput = document.getElementById('rating-value');

                        stars.forEach(star => {
                            star.addEventListener('click', function() {
                                const val = this.getAttribute('data-value');
                                ratingInput.value = val;

                                // Reset & Highlight
                                stars.forEach((s, index) => {
                                    if (index < val) {
                                        s.classList.add('text-yellow-400');
                                        s.classList.remove('text-gray-300');
                                    } else {
                                        s.classList.remove('text-yellow-400');
                                        s.classList.add('text-gray-300');
                                    }
                                });
                            });
                        });

                        // AJAX Filtering logic
                        const filterBtns = document.querySelectorAll('.filter-review-btn');
                        const reviewsContainer = document.getElementById('reviews-list-container');
                        const productId = <?= $id ?>;

                        filterBtns.forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const rating = this.getAttribute('data-rating');

                                // Cập nhật trạng thái UI cho các nút
                                filterBtns.forEach(b => {
                                    b.classList.remove('bg-black', 'text-white', 'border-black');
                                    b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
                                });
                                this.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
                                this.classList.add('bg-black', 'text-white', 'border-black');

                                // Gọi AJAX lấy dữ liệu
                                fetch(`index.php?action=get_reviews&id=${productId}&rating_filter=${rating}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.length === 0) {
                                            reviewsContainer.innerHTML = '<p class="text-gray-500 italic">Sản phẩm này chưa có đánh giá nào.</p>';
                                        } else {
                                            reviewsContainer.innerHTML = data.map(review => `
                                                <div class="flex gap-4 pb-6 border-b border-gray-100 last:border-0">
                                                    <img src="/web-shop-php/asset/${review.avatar || 'default_avatar.png'}" class="w-12 h-12 rounded-full object-cover">
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <h4 class="font-bold">${review.user_name}</h4>
                                                            <div class="flex text-yellow-400 text-sm">
                                                                ${Array(5).fill(0).map((_, i) => `
                                                                    <i class="ri-star-${i < review.rating ? 'fill' : 'line'}"></i>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                        <p class="text-gray-600 text-sm leading-relaxed">${review.content.replace(/\n/g, '<br>')}</p>
                                                    </div>
                                                </div>
                                            `).join('');
                                        }
                                    });
                            });
                        });
                    });
                </script>
            </main>
        <?php endif;

        // Small script: attribute-based selection (color/size) -> pick variant, update image & stock
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const colorButtons = document.querySelectorAll('.color-option');
                const sizeButtons = document.querySelectorAll('.size-option');
                const btnAddToCart = document.querySelector('.btn-add-to-cart');
                const mainImg = document.getElementById('main-image');
                const stockCountDisplay = document.getElementById('stock-count');

                function toNumbers(arr) {
                    return arr.map(x => Number(x));
                }

                function intersect(a, b) {
                    return a.filter(v => b.includes(v));
                }

                let selectedColorVariants = null;
                let selectedSizeVariants = null;

                colorButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        colorButtons.forEach(b => b.classList.remove('ring-2', 'ring-black', 'border-black'));
                        this.classList.add('ring-2', 'ring-black', 'border-black');
                        selectedColorVariants = toNumbers(this.getAttribute('data-variants').split(','));

                        // Update size availability
                        sizeButtons.forEach(s => {
                            const sVariants = toNumbers(s.getAttribute('data-variants').split(','));
                            const inter = intersect(selectedColorVariants, sVariants);
                            if (inter.length > 0) {
                                s.disabled = false;
                                s.classList.remove('opacity-40', 'cursor-not-allowed');
                            } else {
                                s.disabled = true;
                                s.classList.add('opacity-40', 'cursor-not-allowed');
                            }
                        });

                        updateSelectedVariant();
                    });
                });

                sizeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        sizeButtons.forEach(b => b.classList.remove('ring-2', 'ring-black', 'border-black', 'bg-black', 'text-white'));
                        this.classList.add('ring-2', 'ring-black', 'border-black', 'bg-black', 'text-white');
                        selectedSizeVariants = toNumbers(this.getAttribute('data-variants').split(','));
                        updateSelectedVariant();
                    });
                });

                function updateSelectedVariant() {
                    let variantIds = null;
                    if (selectedColorVariants && selectedSizeVariants) variantIds = intersect(selectedColorVariants, selectedSizeVariants);
                    else if (selectedColorVariants) variantIds = selectedColorVariants;
                    else if (selectedSizeVariants) variantIds = selectedSizeVariants;

                    if (variantIds && variantIds.length > 0) {
                        const vid = variantIds[0];
                        if (btnAddToCart) btnAddToCart.setAttribute('data-variant-id', vid);
                        const variantEl = document.querySelector('.variant-option[data-variant-id="' + vid + '"]');
                        if (variantEl) {
                            const stock = variantEl.getAttribute('data-stock') || 0;
                            if (stockCountDisplay) stockCountDisplay.textContent = stock;
                            const img = variantEl.getAttribute('data-image');
                            if (img && mainImg) mainImg.src = img;
                            document.querySelectorAll('.variant-option').forEach(el => el.classList.remove('border-black', 'ring-2', 'ring-black'));
                            variantEl.classList.add('border-black', 'ring-2', 'ring-black');
                        }
                    }
                }
            });
        </script>

    <?php
        // Include footer
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function category()
    {
        $id = $_GET['id'] ?? null;
        $sort = $_GET['sort'] ?? 'newest';
        $manufacturer_id = $_GET['manufacturer_id'] ?? 'all'; // Lấy tham số hãng sản xuất
        $price_range = $_GET['price_range'] ?? 'all';
        $user_id = $_SESSION['user_id'] ?? 0;
        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        // 1. Lấy thông tin tên danh mục để hiển thị tiêu đề trang
        $catQuery = "SELECT category_name FROM categories WHERE category_id = :id";
        $catStmt = $db->prepare($catQuery);
        $catStmt->execute(['id' => $id]);
        $category = $catStmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            header("Location: index.php");
            exit;
        }

        // Lấy danh sách các hãng sản xuất để hiển thị bộ lọc
        $manufacturersQuery = "SELECT manufacturer_id, manufacturer_name FROM manufacturers ORDER BY manufacturer_name ASC";
        $manufacturersStmt = $db->prepare($manufacturersQuery);
        $manufacturersStmt->execute();
        $manufacturers = $manufacturersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Xác định logic sắp xếp dựa trên tham số 'sort'
        $orderBy = "p.created_at DESC";
        if ($sort === 'price_asc') {
            $orderBy = "COALESCE(p.discount_price, p.price) ASC";
        } elseif ($sort === 'price_desc') {
            $orderBy = "COALESCE(p.discount_price, p.price) DESC";
        }

        // Xác định điều kiện lọc giá
        $priceCondition = "";
        if ($price_range === 'under-500') {
            $priceCondition = " AND COALESCE(p.discount_price, p.price) < 500000";
        } elseif ($price_range === '500-2000') {
            $priceCondition = " AND COALESCE(p.discount_price, p.price) BETWEEN 500000 AND 2000000";
        } elseif ($price_range === 'over-2000') {
            $priceCondition = " AND COALESCE(p.discount_price, p.price) > 2000000";
        }

        // Xác định điều kiện lọc theo hãng sản xuất
        $manufacturerCondition = "";
        $params = ['id' => $id, 'user_id' => $user_id];
        if ($manufacturer_id !== 'all') {
            $manufacturerCondition = " AND p.manufacturer_id = :manufacturer_id";
            $params['manufacturer_id'] = $manufacturer_id;
        }


        // 2. Truy vấn lấy tất cả sản phẩm thuộc danh mục này
        $query = "SELECT p.*, c.category_name, m.manufacturer_name, f.farority_id AS is_favorited
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                   LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                   LEFT JOIN favority f ON p.product_id = f.product_id AND f.user_id = :user_id
                  WHERE p.category_id = :id AND p.status = 'active' $priceCondition $manufacturerCondition
                  ORDER BY $orderBy";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Hiển thị View danh sách sản phẩm
        include_once PROJECT_ROOT . '/components/header.php';
    ?>
        <?php
        if ($id == 1):
        ?>
            <div class="mt-35 w-full relative">
                <img src="../asset/banner-shoes2.png" class="">
                <div class="absolute bottom-0 z-100 p-10">
                    <p class="text-white">Giầy thể thao</p>
                    <p class="text-sm text-white opacity-50">Lựa chọn những đôi giày phù hợp với đôi chân bạn.</p>
                </div>
            </div>
        <?php
        elseif ($id == 2): ?>
            <div class="mt-35 w-full relative">
                <div class="grid grid-cols-2">
                    <img src="../asset/banner-shirt-main.png" class="w-full">
                    <img src="../asset/banner-shirt-main2.png" class="w-full">
                </div>
                <div class="absolute bottom-0 z-100 p-7">
                    <p class="text-white">Áo thời trang</p>
                    <p class="text-white text-sm">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Maxime minus totam illo ipsum unde veritatis alias</p>
                </div>
            </div>
        <?php elseif ($id == 3): ?>
            <div class="mt-35 w-full">
                <img src="../asset/banner-pant2.jpg" class="">
            </div>

        <?php elseif ($id == 4): ?>
            <div class="mt-35 w-full relative">
                <img src="../asset/banner-bag2.avif" class="">
                <div class="absolute bottom-0 z-100 p-10">
                    <p>Túi sách tay</a>
                    <p class="text-sm opacity-50">Thoải mái lựa chọn tất cả thương hiệu túi sách chính hãng của chúng tôi.</p>
                </div>
            </div>
        <?php endif; ?>
        <main class="container mx-auto px-7 py-10 mt-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-4">
                <div>
                    <h1 class="text-4xl font-bold uppercase italic tracking-tighter">Category: <?php echo htmlspecialchars($category['category_name']); ?></h1>
                    <p class="text-gray-500 mt-2">Showing <?php echo count($products); ?> results found</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Hãng:</label>
                        <select onchange="updateFilters('manufacturer_id', this.value)" class="bg-white border-none text-sm outline-none cursor-pointer font-medium">
                            <option value="all" <?php echo $manufacturer_id === 'all' ? 'selected' : ''; ?>>Tất cả hãng</option>
                            <?php foreach ($manufacturers as $manufacturer): ?>
                                <option value="<?php echo $manufacturer['manufacturer_id']; ?>" <?php echo (string)$manufacturer_id === (string)$manufacturer['manufacturer_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($manufacturer['manufacturer_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Khoảng giá:</label>
                        <select onchange="updateFilters('price_range', this.value)" class="bg-white border-none text-sm outline-none cursor-pointer font-medium">
                            <option value="all" <?php echo $price_range === 'all' ? 'selected' : ''; ?>>Tất cả giá</option>
                            <option value="under-500" <?php echo $price_range === 'under-500' ? 'selected' : ''; ?>>Dưới 500k</option>
                            <option value="500-2000" <?php echo $price_range === '500-2000' ? 'selected' : ''; ?>>500k - 2tr</option>
                            <option value="over-2000" <?php echo $price_range === 'over-2000' ? 'selected' : ''; ?>>Trên 2tr</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Sắp xếp:</label>
                        <select onchange="updateFilters('sort', this.value)" class="bg-white border-none text-sm outline-none cursor-pointer font-medium">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php if (empty($products)): ?>
                    <div class="col-span-full text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-gray-400">Không tìm thấy sản phẩm nào trong danh mục này.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                            <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>" class="block relative h-64 bg-gray-100">
                                <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="p-4 bg-white/90 absolute bottom-0 w-full translate-y-full group-hover:translate-y-0 transition-transform">
                                    <h3 class="font-bold text-sm truncate"><?php echo $product['name']; ?></h3>
                                    <p class="text-red-600 font-bold"><?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

        <script>
            function updateFilters(key, value) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set(key, value);
                window.location.href = 'index.php?' + urlParams.toString();
            }
        </script>
        <?php
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function getProfileByUser()
    {
        // 1. Kiểm tra xác thực: Nếu chưa đăng nhập thì không cho vào
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;

        // 2. Kiểm tra phân quyền: Đảm bảo người dùng chỉ xem được hồ sơ của chính mình
        if (!$id || (int)$id !== (int)$_SESSION['user_id']) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM users
                  WHERE user_id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách đơn hàng đã mua của người dùng này
        $orderQuery = "SELECT * FROM orders WHERE user_id = :id ORDER BY created_at DESC";
        $orderStmt = $db->prepare($orderQuery);
        $orderStmt->execute(['id' => $id]);
        $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';

        if (!$user): ?>
            <div class="container mx-auto py-20 text-center">
                <p class="text-xl font-medium">Trang không tồn tại hoặc tài khoản đã bị khóa.</p>
            </div>
        <?php else: ?>
            <main class="container mx-auto px-7 py-20 mt-10">
                <div class="max-w-4xl mx-auto space-y-8">
                    <!-- Card Thông tin cá nhân -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <h1 class="text-3xl font-bold mb-8 flex items-center gap-3">
                            <i class="ri-user-settings-line"></i> Thông tin cá nhân
                        </h1>

                        <div class="space-y-5">
                            <div class="flex flex-col items-center mb-6">
                                <img src="/web-shop-php/asset/<?php echo $user['avatar'] ?: 'default_avatar.png'; ?>"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm">
                            </div>
                            <div class="flex border-b border-gray-50 pb-3">
                                <span class="w-40 text-gray-500">Họ và tên:</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></span>
                            </div>
                            <div class="flex border-b border-gray-50 pb-3">
                                <span class="w-40 text-gray-500">Tên đăng nhập:</span>
                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            <div class="flex border-b border-gray-50 pb-3">
                                <span class="w-40 text-gray-500">Email:</span>
                                <span><?php echo htmlspecialchars($user['gmail'] ?? 'Chưa cập nhật'); ?></span>
                            </div>
                            <div class="flex">
                                <span class="w-40 text-gray-500">Số điện thoại:</span>
                                <span><?php echo htmlspecialchars($user['number_phone'] ?? 'Chưa cập nhật'); ?></span>
                            </div>
                        </div>

                        <div class="mt-12 flex gap-4">
                            <a href="index.php?action=edit_profile&id=<?php echo $user['user_id']; ?>" class="bg-black text-white px-8 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition-all text-center">Chỉnh sửa</a>
                            <a href="index.php?action=logout" class="border border-red-500 text-red-500 px-8 py-2.5 rounded-lg font-bold hover:bg-red-50 transition-all text-center">Đăng xuất</a>
                        </div>
                    </div>

                    <!-- Card Lịch sử đơn hàng -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                            <i class="ri-history-line"></i> Lịch sử đơn hàng
                        </h2>

                        <?php if (empty($orders)): ?>
                            <p class="text-gray-500 italic">Bạn chưa có đơn hàng nào.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b text-xs text-gray-400 font-bold uppercase tracking-wider">
                                            <th class="py-3">Mã đơn</th>
                                            <th class="py-3">Ngày đặt</th>
                                            <th class="py-3">Tổng tiền</th>
                                            <th class="py-3 text-center">Trạng thái</th>
                                            <th class="py-3">Thanh toán</th>
                                            <th class="py-3 text-right">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($orders as $order): ?>
                                            <tr class="text-sm hover:bg-gray-50 transition-colors">
                                                <td class="py-4 font-bold text-indigo-600">#<?php echo $order['order_code']; ?></td>
                                                <td class="py-4 text-gray-600"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                <td class="py-4 font-bold"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>₫</td>
                                                <td class="py-4 text-center">
                                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase <?php
                                                                                                                        echo $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                                                                                                        ?>">
                                                        <?php echo $order['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="py-4">
                                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase <?php
                                                                                                                        echo $order['payment_status'] === 'paid' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400';
                                                                                                                        ?>">
                                                        <?php echo $order['payment_status']; ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 text-right">
                                                    <a href="index.php?action=orderDetail&id=<?php echo $order['order_id']; ?>" class="text-blue-600 hover:underline font-medium">Chi tiết</a>
                                                    <?php if ($order['status'] === 'pending'): ?>
                                                        <a href="index.php?action=cancel_order&id=<?php echo $order['order_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')" class="text-red-500 hover:underline font-medium ml-3">Hủy đơn</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        <?php endif;

        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function orderDetail()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $order_id = $_GET['id'] ?? null;
        if (!$order_id) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        // Lấy thông tin đơn hàng và kiểm tra quyền sở hữu (Security check)
        $orderQuery = "SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id";
        $orderStmt = $db->prepare($orderQuery);
        $orderStmt->execute(['order_id' => $order_id, 'user_id' => $_SESSION['user_id']]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $itemsQuery = "SELECT * FROM order_items WHERE order_id = :order_id";
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->execute(['order_id' => $order_id]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';
        ?>
        <main class="container mx-auto px-7 py-20 mt-10">
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <a href="index.php?action=profile&id=<?php echo $_SESSION['user_id']; ?>" class="text-gray-500 hover:text-black transition-colors flex items-center gap-2">
                        <i class="ri-arrow-left-line"></i> Quay lại hồ sơ
                    </a>
                    <div class="flex items-center gap-4">
                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="index.php?action=cancel_order&id=<?php echo $order['order_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition-all flex items-center gap-1">
                                <i class="ri-close-circle-line"></i> Hủy đơn hàng
                            </a>
                        <?php endif; ?>
                        <h1 class="text-2xl font-bold">Chi tiết đơn hàng #<?php echo $order['order_code']; ?></h1>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Danh sách sản phẩm -->
                    <div class="md:col-span-2 space-y-4">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="font-bold mb-4 border-b pb-2">Sản phẩm đã đặt</h3>
                            <div class="space-y-4">
                                <?php foreach ($items as $item): ?>
                                    <div class="flex gap-4 items-center">
                                        <img src="/web-shop-php/asset/<?php echo $item['product_image']; ?>" class="w-16 h-16 object-cover rounded-lg border">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-sm"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                            <p class="text-xs text-gray-500">SKU: <?php echo $item['sku']; ?></p>
                                            <p class="text-sm font-bold mt-1"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫ x <?php echo $item['quantity']; ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold"><?php echo number_format($item['total_price'], 0, ',', '.'); ?>₫</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin nhận hàng & Tổng thanh toán -->
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="font-bold mb-4 border-b pb-2">Thông tin giao hàng</h3>
                            <div class="text-sm space-y-2">
                                <p><span class="text-gray-500">Người nhận:</span> <br> <strong><?php echo htmlspecialchars($order['recipient_name']); ?></strong></p>
                                <p><span class="text-gray-500">Điện thoại:</span> <br> <?php echo htmlspecialchars($order['recipient_phone']); ?></p>
                                <p><span class="text-gray-500">Địa chỉ:</span> <br>
                                    <?php echo htmlspecialchars($order['specific_address']); ?>,
                                    <?php echo htmlspecialchars($order['ward_name']); ?>,
                                    <?php echo htmlspecialchars($order['district_name']); ?>,
                                    <?php echo htmlspecialchars($order['province_name']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="font-bold mb-4 border-b pb-2">Tóm tắt thanh toán</h3>
                            <div class="text-sm space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tạm tính</span>
                                    <span><?php echo number_format($order['subtotal'], 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Phí vận chuyển</span>
                                    <span><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Thuế (10%)</span>
                                    <span><?php echo number_format($order['subtotal'] * 0.1, 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="flex justify-between border-t pt-3 font-bold text-lg text-red-600">
                                    <span>Tổng cộng</span>
                                    <span><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="mt-4 pt-4 border-t space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 uppercase">Trạng thái đơn hàng</span>
                                        <span class="font-bold"><?php echo strtoupper($order['status']); ?></span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 uppercase">Thanh toán</span>
                                        <span class="font-bold text-blue-600"><?php echo strtoupper($order['payment_status']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    <?php
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function cancelOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $order_id = $_GET['id'] ?? null;
        if (!$order_id) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        // Kiểm tra quyền sở hữu và trạng thái đơn hàng (Chỉ cho phép hủy khi đang 'pending')
        $stmt = $db->prepare("SELECT status FROM orders WHERE order_id = :oid AND user_id = :uid");
        $stmt->execute(['oid' => $order_id, 'uid' => $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order && $order['status'] === 'pending') {
            $db->beginTransaction();
            try {
                // 1. Cập nhật trạng thái đơn hàng thành 'cancelled'
                $updateStmt = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = :oid");
                $updateStmt->execute(['oid' => $order_id]);

                // 2. Hoàn trả số lượng vào kho (Restock)
                $itemsStmt = $db->prepare("SELECT product_id, variant_id, quantity FROM order_items WHERE order_id = ?");
                $itemsStmt->execute([$order_id]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

                $updateStock = $db->prepare("UPDATE inventory SET quantity = quantity + :qty WHERE product_id = :pid AND variant_id <=> :vid");
                foreach ($items as $item) {
                    $updateStock->execute(['qty' => $item['quantity'], 'pid' => $item['product_id'], 'vid' => $item['variant_id']]);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                // Log error if needed
            }
        }

        header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
        exit;
    }

    public function editProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id || (int)$id !== (int)$_SESSION['user_id']) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';
    ?>
        <main class="container mx-auto px-7 py-20 mt-10">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h1 class="text-3xl font-bold mb-8 italic">Chỉnh sửa thông tin</h1>

                <form action="index.php?action=update_profile" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                    <div class="flex flex-col items-center mb-4">
                        <img src="/web-shop-php/asset/<?php echo $user['avatar'] ?: 'default_avatar.png'; ?>" class="w-24 h-24 rounded-full object-cover mb-4 border shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thay đổi ảnh đại diện</label>
                        <input type="file" name="avatar" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
                            class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-black outline-none" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="number_phone" value="<?php echo htmlspecialchars($user['number_phone']); ?>"
                            class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-black outline-none">
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="bg-black text-white px-8 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition-all">Lưu thay đổi</button>
                        <a href="index.php?action=profile&id=<?php echo $user['user_id']; ?>" class="bg-gray-200 text-gray-800 px-8 py-2.5 rounded-lg font-bold hover:bg-gray-300 transition-all">Hủy</a>
                    </div>
                </form>
            </div>
        </main>
<?php
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $name = $_POST['name'] ?? '';
            $number_phone = $_POST['number_phone'] ?? '';

            if (!$user_id || (int)$user_id !== (int)$_SESSION['user_id']) {
                header("Location: index.php");
                exit;
            }

            $database = new Database();
            $db = $database->getConnection();

            // Xử lý Avatar
            $avatar_name = $_SESSION['user_avatar'] ?? 'default_avatar.png';
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $avatar_name = "avatar_" . $user_id . "_" . time() . "." . $file_ext;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], PROJECT_ROOT . '/asset/' . $avatar_name);
                }
            }

            $query = "UPDATE users SET name = :name, number_phone = :phone, avatar = :avatar WHERE user_id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'name' => $name,
                'phone' => $number_phone,
                'avatar' => $avatar_name,
                'id' => $user_id
            ]);

            $_SESSION['user_name'] = $name;
            $_SESSION['user_avatar'] = $avatar_name;

            header("Location: index.php?action=profile&id=" . $user_id);
            exit;
        }
    }

    public function register()
    {
        include_once PROJECT_ROOT . '/views/register.php';
    }

    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $gender = $_POST['gender'] ?? '0';
            $number_phone = $_POST['number_phone'] ?? '';
            $gmail = $_POST['gmail'] ?? '';

            $database = new Database();
            $db = $database->getConnection();

            // Kiểm tra username đã tồn tại chưa
            $checkQuery = "SELECT user_id FROM users WHERE username = :username";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->execute(['username' => $username]);

            if ($checkStmt->rowCount() > 0) {
                $error = "Tên đăng nhập đã tồn tại!";
                include_once PROJECT_ROOT . '/views/register.php';
                return;
            }

            // Hash the password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $query = "INSERT INTO users (name, username, password, gender, number_phone, gmail, role, status) 
                          VALUES (:name, :username, :password, :gender, :phone, :gmail, 'customer', 'active')";

                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    'name' => $name,
                    'username' => $username,
                    'password' => $hashedPassword,
                    'gender' => $gender,
                    'phone' => $number_phone,
                    'gmail' => $gmail
                ]);

                if ($result) {
                    header("Location: index.php?action=login&register_success=1");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Có lỗi xảy ra: " . $e->getMessage();
                include_once PROJECT_ROOT . '/views/register.php';
            }
        }
    }

    public function login()
    {
        if (isset($_GET['redirect'])) {
            $_SESSION['redirect'] = $_GET['redirect'];
        }
        include_once PROJECT_ROOT . '/views/login.php';
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the hashed password
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? 'default_avatar.png';

                require_once PROJECT_ROOT . '/app/CartController.php';
                $cartCtrl = new CartController();
                $cartCtrl->syncSessionCartToDb($user['user_id']);

                // Chuyển hướng dựa trên role
                if (isset($_SESSION['redirect'])) {
                    $redirect = $_SESSION['redirect'];
                    unset($_SESSION['redirect']);
                    header("Location: index.php?action=" . $redirect);
                    exit;
                }

                if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
                include_once PROJECT_ROOT . '/views/login.php';
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public function toggleFavorite()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện!']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'] ?? null;

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        // Kiểm tra xem sản phẩm đã nằm trong mục yêu thích của User này chưa
        $checkQuery = "SELECT farority_id FROM favority WHERE user_id = :user_id AND product_id = :product_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
        $favorite = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($favorite) {
            // Nếu đã tồn tại thì thực hiện xóa (Unlike)
            $deleteQuery = "DELETE FROM favority WHERE farority_id = :id";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->execute(['id' => $favorite['farority_id']]);

            echo json_encode([
                'success' => true,
                'isFavorited' => false,
                'message' => 'Đã xóa khỏi danh sách yêu thích!'
            ]);
        } else {
            // Nếu chưa tồn tại thì thực hiện thêm mới (Like)
            $insertQuery = "INSERT INTO favority (user_id, product_id) VALUES (:user_id, :product_id)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);

            echo json_encode([
                'success' => true,
                'isFavorited' => true,
                'message' => 'Đã thêm vào danh sách yêu thích!'
            ]);
        }
    }

    public function search()
    {
        header('Content-Type: application/json');
        $keyword = $_GET['keyword'] ?? '';

        if (strlen($keyword) < 2) {
            echo json_encode([]);
            return;
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT product_id, name, price, discount_price, thumbnail 
                  FROM products 
                  WHERE name LIKE :keyword AND status = 'active' 
                  LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute(['keyword' => "%$keyword%"]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($products);
    }

    public function addReview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $product_id = $_POST['product_id'] ?? null;
            $rating = $_POST['rating'] ?? 5;
            $content = $_POST['content'] ?? '';

            if ($product_id && $content) {
                $database = new Database();
                $db = $database->getConnection();

                $query = "INSERT INTO reviews (user_id, product_id, content, rating) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$user_id, $product_id, $content, $rating]);

                header("Location: index.php?action=detail&id=" . $product_id);
                exit;
            }
        }

        header("Location: index.php");
        exit;
    }

    public function getReviews()
    {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        $ratingFilter = $_GET['rating_filter'] ?? 'all';

        if (!$id) {
            echo json_encode([]);
            return;
        }

        $database = new Database();
        $db = $database->getConnection();

        $reviewParams = ['id' => $id];
        $reviewQuery = "SELECT r.*, u.name as user_name, u.avatar 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.user_id 
                        WHERE r.product_id = :id";

        if ($ratingFilter !== 'all') {
            $reviewQuery .= " AND r.rating = :rating";
            $reviewParams['rating'] = (int)$ratingFilter;
        }

        $reviewQuery .= " ORDER BY r.review_id DESC";
        $rStmt = $db->prepare($reviewQuery);
        $rStmt->execute($reviewParams);
        echo json_encode($rStmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function bestSellers()
    {
        $database = new Database();
        $db = $database->getConnection();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $category_id = isset($_GET['category_id']) && $_GET['category_id'] !== 'all' ? (int)$_GET['category_id'] : null;
        $price_range = $_GET['price_range'] ?? 'all';
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $user_id = $_SESSION['user_id'] ?? 0;

        // Lấy danh sách danh mục để hiển thị bộ lọc
        $categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

        // Xây dựng điều kiện WHERE
        $where = " WHERE p.status = 'active'";
        $params = [];
        if ($category_id) {
            $where .= " AND p.category_id = :category_id";
            $params['category_id'] = $category_id;
        }

        if ($price_range !== 'all') {
            if ($price_range === 'under-500') {
                $where .= " AND COALESCE(p.discount_price, p.price) < 500000";
            } elseif ($price_range === '500-2000') {
                $where .= " AND COALESCE(p.discount_price, p.price) BETWEEN 500000 AND 2000000";
            } elseif ($price_range === 'over-2000') {
                $where .= " AND COALESCE(p.discount_price, p.price) > 2000000";
            }
        }

        // Lấy tổng số sản phẩm duy nhất đã bán để tính số trang
        $countQuery = "SELECT COUNT(DISTINCT oi.product_id) 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       $where";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute($params);
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $limit);

        // Lấy danh sách sản phẩm bán chạy nhất kèm trạng thái yêu thích
        $query = "SELECT p.*, c.category_name, SUM(oi.quantity) AS total_sold, f.farority_id AS is_favorited
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.product_id 
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  LEFT JOIN favority f ON p.product_id = f.product_id AND f.user_id = :user_id
                  $where
                  GROUP BY p.product_id
                  ORDER BY total_sold DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        if ($category_id) {
            $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/best_sellers.php';
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function products()
    {
        $database = new Database();
        $db = $database->getConnection();

        $user_id = $_SESSION['user_id'] ?? 0;
        $limit = 8;
        $categoryKeys = [
            'shoes' => 'Giày',
            'bags' => 'Túi',
            'shirt' => 'Áo',
            'pants' => 'Quần'
        ];

        // Fetch category ids for our keys
        $names = array_keys($categoryKeys);
        $placeholders = implode(',', array_fill(0, count($names), '?'));
        $stmt = $db->prepare("SELECT * FROM categories WHERE category_name IN ($placeholders)");
        $stmt->execute($names);
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catMap = [];
        foreach ($cats as $c) { $catMap[$c['category_name']] = $c['category_id']; }

        $categories = [];
        foreach ($categoryKeys as $catName => $title) {
            $cid = $catMap[$catName] ?? null;
            $pageParam = 'page_' . $catName;
            $currentPage = isset($_GET[$pageParam]) ? max(1, (int)$_GET[$pageParam]) : 1;
            $offset = ($currentPage - 1) * $limit;

            if (!$cid) {
                $categories[$catName] = ['title' => $title, 'products' => [], 'current_page' => 1, 'total_pages' => 1, 'category_id' => null];
                continue;
            }

            $countStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = :cid AND status = 'active'");
            $countStmt->execute(['cid' => $cid]);
            $total = (int)$countStmt->fetchColumn();
            $totalPages = max(1, ceil($total / $limit));

            $q = "SELECT p.*, c.category_name, f.farority_id AS is_favorited, i.available_quantity
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  LEFT JOIN favority f ON p.product_id = f.product_id AND f.user_id = :user_id
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.category_id = :cid AND p.status = 'active'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";
            $pstmt = $db->prepare($q);
            $pstmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $pstmt->bindValue(':cid', $cid, PDO::PARAM_INT);
            $pstmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $pstmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $pstmt->execute();
            $products = $pstmt->fetchAll(PDO::FETCH_ASSOC);

            $categories[$catName] = [
                'title' => $title,
                'products' => $products,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'category_id' => $cid
            ];
        }

        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/products.php';
        include_once PROJECT_ROOT . '/components/footer.php';
    }
}
