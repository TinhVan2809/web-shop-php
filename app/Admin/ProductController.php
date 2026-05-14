<?php

require_once __DIR__ . '/AdminBaseController.php';

class ProductController extends AdminBaseController
{
    // --- SẢN PHẨM ---
    public function list()
    {
        $query = "SELECT p.*, c.category_name, m.manufacturer_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                  ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('products/list', ['products' => $products]);
    }

    public function form()
    {
        $id = $_GET['id'] ?? null;
        $product = null;
        $extra_images = [];
        $variants = [];

        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Lấy danh sách ảnh phụ hiện có
            $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ?");
            $stmt->execute([$id]);
            $extra_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Lấy danh sách biến thể kèm thuộc tính và tồn kho
            $vStmt = $this->db->prepare("
                SELECT pv.*, i.quantity as stock, 
                       GROUP_CONCAT(CONCAT(va.attribute_name, ':', va.attribute_value)) as attr_string
                FROM product_variants pv
                LEFT JOIN variant_attributes va ON pv.variant_id = va.variant_id
                LEFT JOIN inventory i ON pv.variant_id = i.variant_id
                WHERE pv.product_id = ?
                GROUP BY pv.variant_id
            ");
            $vStmt->execute([$id]);
            $variants = $vStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $categories = $this->db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        $manufacturers = $this->db->query("SELECT * FROM manufacturers")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('products/form', [
            'product' => $product,
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'extra_images' => $extra_images,
            'variants' => $variants
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['product_id'] ?? null;
            $name = $_POST['name'];
            $price = $_POST['price'];
            $discount_price = $_POST['discount_price'] ?: null;
            $category_id = $_POST['category_id'];
            $manufacturer_id = $_POST['manufacturer_id'];
            $sku = $_POST['sku'];
            $status = $_POST['status'];
            $description = $_POST['description'];
            $thumbnail = $_POST['current_thumbnail'];

            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
                $filename = time() . '_' . $_FILES['thumbnail']['name'];
                move_uploaded_file($_FILES['thumbnail']['tmp_name'], PROJECT_ROOT . '/asset/' . $filename);
                $thumbnail = $filename;
            }

            if ($id) {
                $query = "UPDATE products SET name=?, price=?, discount_price=?, category_id=?, manufacturer_id=?, sku=?, status=?, description=?, thumbnail=? WHERE product_id=?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$name, $price, $discount_price, $category_id, $manufacturer_id, $sku, $status, $description, $thumbnail, $id]);
            } else {
                $query = "INSERT INTO products (name, price, discount_price, category_id, manufacturer_id, sku, status, description, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$name, $price, $discount_price, $category_id, $manufacturer_id, $sku, $status, $description, $thumbnail]);
                $id = $this->db->lastInsertId();
            }

            // --- XỬ LÝ BIẾN THỂ (VARIANTS) ---
            if (isset($_POST['variants'])) {
                // Để đơn giản, chúng ta xóa các biến thể cũ, inventory liên quan và insert mới
                // (Lưu ý: Trong thực tế nên cập nhật theo ID để tránh ảnh hưởng đến khóa ngoại nếu có đơn hàng)
                // Xóa bản ghi `inventory` trước để tránh lỗi ràng buộc foreign key (inventory.variant_id -> product_variants.variant_id)
                $this->db->prepare("DELETE FROM inventory WHERE product_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);

                foreach ($_POST['variants'] as $idx => $v) {
                    if (empty($v['sku'])) continue;

                    $v_image = $v['current_image'] ?? null;
                    // Xử lý upload ảnh riêng cho từng biến thể
                    if (isset($_FILES['variant_images']['name'][$idx]) && $_FILES['variant_images']['error'][$idx] == 0) {
                        $v_filename = time() . '_variant_' . $idx . '_' . $_FILES['variant_images']['name'][$idx];
                        move_uploaded_file($_FILES['variant_images']['tmp_name'][$idx], PROJECT_ROOT . '/asset/' . $v_filename);
                        $v_image = $v_filename;
                    }

                    // 1. Insert product_variants
                    $stmtV = $this->db->prepare("INSERT INTO product_variants (product_id, sku, price, image) VALUES (?, ?, ?, ?)");
                    $stmtV->execute([$id, $v['sku'], $v['price'] ?: null, $v_image]);
                    $variant_id = $this->db->lastInsertId();

                    // 2. Insert variant_attributes (Size, Color...)
                    if (!empty($v['attrs'])) {
                        $stmtA = $this->db->prepare("INSERT INTO variant_attributes (variant_id, attribute_name, attribute_value) VALUES (?, ?, ?)");
                        foreach ($v['attrs'] as $attr_name => $attr_value) {
                            if (!empty($attr_value)) {
                                $stmtA->execute([$variant_id, $attr_name, $attr_value]);
                            }
                        }
                    }

                    // 3. Cập nhật inventory
                    $stmtI = $this->db->prepare("INSERT INTO inventory (product_id, variant_id, quantity) VALUES (?, ?, ?)");
                    $stmtI->execute([$id, $variant_id, $v['stock'] ?: 0]);
                }
            }
        }
        // Kiểm tra xem người dùng có chọn ảnh phụ không
        if (isset($_FILES['extra_images']) && !empty($_FILES['extra_images']['name'][0])) {
            $extra_files = $_FILES['extra_images'];
            $upload_dir = PROJECT_ROOT . '/asset/';

            // Duyệt qua từng file được upload
            for ($i = 0; $i < count($extra_files['name']); $i++) {
                if ($extra_files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_name = $extra_files['name'][$i];
                    $tmp_name = $extra_files['tmp_name'][$i];

                    // Tạo tên file duy nhất để tránh trùng lặp
                    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                    $unique_name = "product_" . $id . "_gallery_" . uniqid() . "." . $extension;

                    // Di chuyển file vào thư mục asset
                    if (move_uploaded_file($tmp_name, $upload_dir . $unique_name)) {
                        // Lưu đường dẫn ảnh vào bảng product_images
                        $imgQuery = "INSERT INTO product_images (product_id, image) VALUES (:product_id, :image)";
                        $imgStmt = $this->db->prepare($imgQuery);
                        $imgStmt->execute([
                            'product_id' => $id,
                            'image' => $unique_name
                        ]);
                    }
                }
            }
        }
        header("Location: index.php?action=admin_products");
        exit;
    }

    public function deleteImage()
    {
        $image_id = $_GET['id'] ?? null;
        $product_id = $_GET['product_id'] ?? null;
        
        if ($image_id) {
            // Lấy thông tin ảnh để xóa file vật lý
            $stmt = $this->db->prepare("SELECT image FROM product_images WHERE image_id = ?");
            $stmt->execute([$image_id]);
            $img = $stmt->fetchColumn();

            if ($img && file_exists(PROJECT_ROOT . '/asset/' . $img)) {
                unlink(PROJECT_ROOT . '/asset/' . $img);
            }

            // Xóa bản ghi trong database
            $stmt = $this->db->prepare("DELETE FROM product_images WHERE image_id = ?");
            $stmt->execute([$image_id]);
        }
        header("Location: index.php?action=product_form&id=" . $product_id);
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                // 1. Lấy tên file ảnh (thumbnail và ảnh phụ) để xóa file vật lý sau này
                $stmt = $this->db->prepare("SELECT thumbnail FROM products WHERE product_id = ?");
                $stmt->execute([$id]);
                $thumb = $stmt->fetchColumn();

                $stmt = $this->db->prepare("SELECT image FROM product_images WHERE product_id = ?");
                $stmt->execute([$id]);
                $extra_imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 2. Xóa các ràng buộc ở các bảng phụ (Ảnh phụ, Yêu thích, Giỏ hàng, Kho)
                // Việc này giúp tránh lỗi "Foreign key constraint fails"
                $this->db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM favority WHERE product_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM carts WHERE product_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM inventory WHERE product_id = ?")->execute([$id]);

                // 3. Xóa sản phẩm chính trong bảng products
                $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->execute([$id]);

                // 4. Dọn dẹp file ảnh trong thư mục asset
                if ($thumb && file_exists(PROJECT_ROOT . '/asset/' . $thumb)) unlink(PROJECT_ROOT . '/asset/' . $thumb);
                foreach ($extra_imgs as $img) {
                    $path = PROJECT_ROOT . '/asset/' . $img['image'];
                    if (file_exists($path)) unlink($path);
                }
            } catch (PDOException $e) {
                // Nếu vẫn lỗi (thường do bảng order_items - không nên xóa sản phẩm đã có người mua)
                die("Không thể xóa sản phẩm: Sản phẩm này đã có trong đơn hàng của khách khách. Bạn nên chuyển trạng thái sang 'Ngừng kinh doanh' thay vì xóa.");
            }
        }
        header("Location: index.php?action=admin_products");
        exit;
    }

    // --- DANH MỤC ---
    public function categories()
    {
        $categories = $this->db->query("SELECT * FROM categories ORDER BY category_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->render('categories/list', ['categories' => $categories]);
    }

    public function categoryForm()
    {
        $id = $_GET['id'] ?? null;
        $category = null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE category_id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $this->render('categories/form', ['category' => $category]);
    }

    public function saveCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['category_id'] ?? null;
            $name = $_POST['category_name'];
            if ($id) {
                $this->db->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?")->execute([$name, $id]);
            } else {
                $this->db->prepare("INSERT INTO categories (category_name) VALUES (?)")->execute([$name]);
            }
        }
        header("Location: index.php?action=admin_categories");
        exit;
    }

    public function deleteCategory()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->db->prepare("DELETE FROM categories WHERE category_id = ?")->execute([$id]);
        }
        header("Location: index.php?action=admin_categories");
        exit;
    }

    // --- THƯƠNG HIỆU ---
    public function manufacturers()
    {
        $manufacturers = $this->db->query("SELECT * FROM manufacturers ORDER BY manufacturer_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->render('manufacturers/list', ['manufacturers' => $manufacturers]);
    }

    public function manufacturerForm()
    {
        $id = $_GET['id'] ?? null;
        $manufacturer = null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM manufacturers WHERE manufacturer_id = ?");
            $stmt->execute([$id]);
            $manufacturer = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $this->render('manufacturers/form', ['manufacturer' => $manufacturer]);
    }

    public function saveManufacturer()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['manufacturer_id'] ?? null;
            $name = $_POST['manufacturer_name'];
            if ($id) {
                $this->db->prepare("UPDATE manufacturers SET manufacturer_name = ? WHERE manufacturer_id = ?")->execute([$name, $id]);
            } else {
                $this->db->prepare("INSERT INTO manufacturers (manufacturer_name) VALUES (?)")->execute([$name]);
            }
        }
        header("Location: index.php?action=admin_manufacturers");
        exit;
    }

    public function deleteManufacturer()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->db->prepare("DELETE FROM manufacturers WHERE manufacturer_id = ?")->execute([$id]);
        }
        header("Location: index.php?action=admin_manufacturers");
        exit;
    }
}
