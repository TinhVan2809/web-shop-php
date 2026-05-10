<?php

require_once __DIR__ . '/AdminBaseController.php';

class VoucherController extends AdminBaseController
{
    public function list()
    {
        $stmt = $this->db->prepare("SELECT * FROM vouchers ORDER BY created_at DESC, voucher_id DESC");
        $stmt->execute();
        $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vouchers as &$voucher) {
            $voucher['categories'] = $this->db->prepare("\n                SELECT c.category_id, c.category_name\n                FROM voucher_categories vc\n                INNER JOIN categories c ON c.category_id = vc.category_id\n                WHERE vc.voucher_id = ?\n                ORDER BY c.category_name ASC\n            ");
            $voucher['categories']->execute([$voucher['voucher_id']]);
            $voucher['categories'] = $voucher['categories']->fetchAll(PDO::FETCH_ASSOC);

            $voucher['products'] = $this->db->prepare("\n                SELECT p.product_id, p.name\n                FROM voucher_products vp\n                INNER JOIN products p ON p.product_id = vp.product_id\n                WHERE vp.voucher_id = ?\n                ORDER BY p.name ASC\n            ");
            $voucher['products']->execute([$voucher['voucher_id']]);
            $voucher['products'] = $voucher['products']->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($voucher);

        $this->render('vouchers/list', ['vouchers' => $vouchers]);
    }

    public function form()
    {
        $id = $_GET['id'] ?? null;
        $voucher = null;
        $selectedCategoryIds = [];
        $selectedProductIds = [];

        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE voucher_id = ?");
            $stmt->execute([$id]);
            $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT category_id FROM voucher_categories WHERE voucher_id = ?");
            $stmt->execute([$id]);
            $selectedCategoryIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');

            $stmt = $this->db->prepare("SELECT product_id FROM voucher_products WHERE voucher_id = ?");
            $stmt->execute([$id]);
            $selectedProductIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'product_id');
        }

        $categories = $this->db->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $products = $this->db->query("SELECT product_id, name, category_id FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('vouchers/form', [
            'voucher' => $voucher,
            'categories' => $categories,
            'products' => $products,
            'selectedCategoryIds' => $selectedCategoryIds,
            'selectedProductIds' => $selectedProductIds,
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=admin_vouchers");
            exit;
        }

        $id = $_POST['voucher_id'] ?? null;
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $discountType = $_POST['discount_type'] ?? 'percent';
        $discountValue = $_POST['discount_value'] ?? 0;
        $maxDiscount = ($_POST['max_discount'] ?? '') !== '' ? $_POST['max_discount'] : null;
        $minOrderValue = $_POST['min_order_value'] ?? 0;
        $usageLimit = ($_POST['usage_limit'] ?? '') !== '' ? (int)$_POST['usage_limit'] : null;
        $startDate = ($_POST['start_date'] ?? '') !== '' ? str_replace('T', ' ', $_POST['start_date']) . ':00' : null;
        $endDate = ($_POST['end_date'] ?? '') !== '' ? str_replace('T', ' ', $_POST['end_date']) . ':00' : null;
        $status = $_POST['status'] ?? 'active';
        $categoryIds = $_POST['category_ids'] ?? [];
        $productIds = $_POST['product_ids'] ?? [];

        if ($code === '') {
            header("Location: index.php?action=voucher_form" . ($id ? "&id={$id}" : ''));
            exit;
        }

        $duplicateStmt = $this->db->prepare("SELECT voucher_id FROM vouchers WHERE code = ?" . ($id ? " AND voucher_id <> ?" : "") . " LIMIT 1");
        $duplicateStmt->execute($id ? [$code, $id] : [$code]);
        $duplicateId = $duplicateStmt->fetchColumn();

        if ($duplicateId) {
            $voucher = [
                'voucher_id' => $id,
                'code' => $code,
                'description' => $description,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'max_discount' => $maxDiscount,
                'min_order_value' => $minOrderValue,
                'usage_limit' => $usageLimit,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ];
            $categories = $this->db->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
            $products = $this->db->query("SELECT product_id, name, category_id FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

            $this->render('vouchers/form', [
                'voucher' => $voucher,
                'categories' => $categories,
                'products' => $products,
                'selectedCategoryIds' => $categoryIds,
                'selectedProductIds' => $productIds,
                'error' => 'Mã voucher đã tồn tại, vui lòng chọn mã khác!'
            ]);
            return;
        }

        if ($discountType === 'percent') {
            $discountValue = min(100, (float)$discountValue);
        }

        try {
            $this->db->beginTransaction();

            if ($id) {
                $stmt = $this->db->prepare("UPDATE vouchers SET code = ?, description = ?, discount_type = ?, discount_value = ?, max_discount = ?, min_order_value = ?, usage_limit = ?, start_date = ?, end_date = ?, status = ? WHERE voucher_id = ?");
                $stmt->execute([
                    $code,
                    $description !== '' ? $description : null,
                    $discountType,
                    $discountValue,
                    $maxDiscount,
                    $minOrderValue,
                    $usageLimit,
                    $startDate,
                    $endDate,
                    $status,
                    $id,
                ]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO vouchers (code, description, discount_type, discount_value, max_discount, min_order_value, usage_limit, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $code,
                    $description !== '' ? $description : null,
                    $discountType,
                    $discountValue,
                    $maxDiscount,
                    $minOrderValue,
                    $usageLimit,
                    $startDate,
                    $endDate,
                    $status,
                ]);
                $id = $this->db->lastInsertId();
            }

            $this->db->prepare("DELETE FROM voucher_categories WHERE voucher_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM voucher_products WHERE voucher_id = ?")->execute([$id]);

            if (!empty($categoryIds)) {
                $stmtCategory = $this->db->prepare("INSERT INTO voucher_categories (voucher_id, category_id) VALUES (?, ?)");
                foreach ($categoryIds as $categoryId) {
                    $stmtCategory->execute([$id, $categoryId]);
                }
            }

            if (!empty($productIds)) {
                $stmtProduct = $this->db->prepare("INSERT INTO voucher_products (voucher_id, product_id) VALUES (?, ?)");
                foreach ($productIds as $productId) {
                    $stmtProduct->execute([$id, $productId]);
                }
            }

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            die('Không thể lưu voucher: ' . $e->getMessage());
        }

        header("Location: index.php?action=admin_vouchers");
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->db->prepare("DELETE FROM voucher_categories WHERE voucher_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM voucher_products WHERE voucher_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM vouchers WHERE voucher_id = ?")->execute([$id]);
        }

        header("Location: index.php?action=admin_vouchers");
        exit;
    }
}
