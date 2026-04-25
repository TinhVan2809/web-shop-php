<?php

require_once PROJECT_ROOT . '/app/Database.php';

class Controller
{
    public function index()
    {
        $database = new Database();
        $db = $database->getConnection();

        // Truy vấn lấy sản phẩm kèm tên danh mục
        $query = "SELECT p.*, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE p.status = 'active' 
                  ORDER BY p.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Include header (đã có sẵn HTML, Head, Body mở)
        include_once PROJECT_ROOT . '/components/header.php';
?>

        <main class="container mx-auto px-7 py-10">
            <!-- <h1 class="text-3xl font-bold mb-8">Sản phẩm mới nhất</h1> -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>" class="block relative h-64 bg-gray-100">
                            <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>"
                                alt="<?php echo $product['name']; ?>"
                                class="w-full h-full object-cover">
                            <?php if ($product['is_new']): ?>
                                <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">Mới</span>
                            <?php endif; ?>
                        </a>

                        <div class="p-4">
                            <p class="text-xs text-gray-500 uppercase"><?php echo $product['category_name']; ?></p>
                            <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>">
                                <h2 class="font-bold text-lg mb-2 truncate hover:text-blue-600 transition-colors"><?php echo $product['name']; ?></h2>
                            </a>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo $product['short_description']; ?></p>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-600 font-bold"><?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫</p>
                                    <?php if ($product['discount_price']): ?>
                                        <p class="text-gray-400 text-xs line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                                    <?php endif; ?>
                                </div>
                                <button class="bg-black text-white p-2 rounded-full hover:bg-gray-800 transition-colors">
                                    <i class="ri-shopping-cart-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php
        // Bạn có thể thêm footer tại đây nếu có
        echo "</body></html>";
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

        $query = "SELECT p.*, c.category_name, m.manufacturer_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                  WHERE p.product_id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';

        if (!$product): ?>
            <div class="container mx-auto px-7 py-20 text-center">
                <h1 class="text-2xl font-bold mb-4">Sản phẩm không tồn tại!</h1>
                <a href="index.php" class="text-blue-600 hover:underline">Quay lại trang chủ</a>
            </div>
        <?php else: ?>
            <main class="container mx-auto px-7 py-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" alt="<?php echo $product['name']; ?>" class="w-full rounded-2xl shadow-lg">
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold mb-2"><?php echo $product['name']; ?></h1>
                        <p class="text-gray-500 mb-6 uppercase tracking-wider text-sm"><?php echo $product['category_name']; ?> | <?php echo $product['manufacturer_name'] ?? 'Haseki Store'; ?></p>
                        <div class="text-3xl font-bold text-red-600 mb-6">
                            <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫
                            <?php if ($product['discount_price']): ?>
                                <span class="text-gray-400 text-xl line-through ml-3"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="prose max-w-none text-gray-700 mb-8 leading-relaxed">
                            <?php echo nl2br($product['description'] ?: $product['short_description']); ?>
                        </div>
                        <div class="">
                            <button class="bg-black text-white px-10 py-4 rounded-full font-bold hover:bg-gray-800 transition-all">
                                THÊM VÀO GIỎ HÀNG
                            </button>
                            <button class="py-3 px-3.5 border border-gray-200"><i class="ri-heart-line text-2xl"></i></button>
                        </div>
                    </div>
                </div>
            </main>
<?php endif;
        echo "</body></html>";
    }

    public function login()
    {
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

            // Ghi chú: Trong thực tế nên dùng password_hash và password_verify
            // Ở đây tôi giả định password lưu trong DB đang ở dạng text thuần hoặc khớp trực tiếp
            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Chuyển hướng dựa trên role
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
}
