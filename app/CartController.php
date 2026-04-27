<?php

require_once PROJECT_ROOT . '/app/Database.php';

class CartController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize session cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index()
    {
        $cartItems = $this->getCartItems();
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item['discount_price'] ?? $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        
        $tax = $subtotal * 0.10; // 10% tax mock
        $shipping = ($subtotal > 0) ? 30000 : 0; // 30k mock shipping
        $total = $subtotal + $tax + $shipping;

        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/cart.php';
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $variant_id = $_POST['variant_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if (!$product_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid product.']);
                return;
            }

            if (isset($_SESSION['user_id'])) {
                $this->addToDbCart($_SESSION['user_id'], $product_id, $quantity, $variant_id);
            } else {
                $this->addToSessionCart($product_id, $quantity, $variant_id);
            }

            $cartCount = $this->getCartCount();
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'cartCount' => $cartCount]);
            return;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $variant_id = $_POST['variant_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if ($quantity < 1) $quantity = 1;

            if (isset($_SESSION['user_id'])) {
                $stmt = $this->db->prepare("UPDATE carts SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id AND variant_id <=> :variant_id");
                $stmt->execute(['quantity' => $quantity, 'user_id' => $_SESSION['user_id'], 'product_id' => $product_id, 'variant_id' => $variant_id]);
            } else {
                $key = $variant_id ? $product_id . ':' . $variant_id : $product_id;
                if (isset($_SESSION['cart'][$key])) {
                    $_SESSION['cart'][$key] = $quantity;
                }
            }

            // Recalculate totals
            $cartItems = $this->getCartItems();
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $price = $item['discount_price'] ?? $item['price'];
                $subtotal += $price * $item['quantity'];
            }
            $tax = $subtotal * 0.10;
            $shipping = ($subtotal > 0) ? 30000 : 0;
            $total = $subtotal + $tax + $shipping;

            echo json_encode([
                'success' => true, 
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'tax' => number_format($tax, 0, ',', '.'),
                'shipping' => number_format($shipping, 0, ',', '.'),
                'total' => number_format($total, 0, ',', '.'),
                'cartCount' => $this->getCartCount()
            ]);
            return;
        }
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $variant_id = $_POST['variant_id'] ?? null;

            if (isset($_SESSION['user_id'])) {
                $stmt = $this->db->prepare("DELETE FROM carts WHERE user_id = :user_id AND product_id = :product_id AND variant_id <=> :variant_id");
                $stmt->execute(['user_id' => $_SESSION['user_id'], 'product_id' => $product_id, 'variant_id' => $variant_id]);
            } else {
                $key = $variant_id ? $product_id . ':' . $variant_id : $product_id;
                if (isset($_SESSION['cart'][$key])) {
                    unset($_SESSION['cart'][$key]);
                }
            }
            
            // Recalculate totals
            $cartItems = $this->getCartItems();
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $price = $item['discount_price'] ?? $item['price'];
                $subtotal += $price * $item['quantity'];
            }
            $tax = $subtotal * 0.10;
            $shipping = ($subtotal > 0) ? 30000 : 0;
            $total = $subtotal + $tax + $shipping;

            $cartCount = $this->getCartCount();
            echo json_encode([
                'success' => true, 
                'cartCount' => $cartCount,
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'tax' => number_format($tax, 0, ',', '.'),
                'shipping' => number_format($shipping, 0, ',', '.'),
                'total' => number_format($total, 0, ',', '.')
            ]);
            return;
        }
    }

    private function addToSessionCart($product_id, $quantity, $variant_id = null)
    {
        $key = $variant_id ? $product_id . ':' . $variant_id : $product_id;
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key] += $quantity;
        } else {
            $_SESSION['cart'][$key] = $quantity;
        }
    }

    private function addToDbCart($user_id, $product_id, $quantity, $variant_id = null)
    {
        // Check if exists
        $stmt = $this->db->prepare("SELECT quantity FROM carts WHERE user_id = :user_id AND product_id = :product_id AND variant_id <=> :variant_id");
        $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $updateStmt = $this->db->prepare("UPDATE carts SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id AND variant_id <=> :variant_id");
            $updateStmt->execute(['quantity' => $newQuantity, 'user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id]);
        } else {
            $insertStmt = $this->db->prepare("INSERT INTO carts (user_id, product_id, variant_id, quantity) VALUES (:user_id, :product_id, :variant_id, :quantity)");
            $insertStmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id, 'quantity' => $quantity]);
        }
    }

    public function syncSessionCartToDb($user_id)
    {
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $quantity) {
                $parts = explode(':', $key);
                $product_id = $parts[0];
                $variant_id = $parts[1] ?? null;
                $this->addToDbCart($user_id, $product_id, $quantity, $variant_id);
            }
            $_SESSION['cart'] = []; // Clear session cart after sync
        }
    }

    private function getCartItems()
    {
        $items = [];
        
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                SELECT c.quantity, c.variant_id, p.product_id, p.name, p.price, p.discount_price, p.thumbnail 
                FROM carts c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE c.user_id = :user_id
            ");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $key => $quantity) {
                    $parts = explode(':', $key);
                    $pid = $parts[0];
                    $vid = $parts[1] ?? null;

                    $stmt = $this->db->prepare("SELECT product_id, name, price, discount_price, thumbnail FROM products WHERE product_id = ?");
                    $stmt->execute([$pid]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        $product['quantity'] = $quantity;
                        $product['variant_id'] = $vid;
                        $items[] = $product;
                    }
                }
            }
        }
        
        return $items;
    }

    public function getCartCount()
    {
        $count = 0;
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM carts WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['total'] ?? 0;
        } else {
            if (!empty($_SESSION['cart'])) {
                $count = array_sum($_SESSION['cart']);
            }
        }
        return (int)$count;
    }
}
