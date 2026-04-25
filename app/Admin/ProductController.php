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
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $categories = $this->db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        $manufacturers = $this->db->query("SELECT * FROM manufacturers")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('products/form', [
            'product' => $product,
            'categories' => $categories,
            'manufacturers' => $manufacturers
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
            }
        }
        header("Location: index.php?action=admin_products");
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$id]);
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
