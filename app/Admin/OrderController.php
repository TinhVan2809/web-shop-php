<?php

require_once __DIR__ . '/AdminBaseController.php';

class OrderController extends AdminBaseController
{
    public function list()
    {
        $status = $_GET['status'] ?? null;
        $query = "SELECT o.*, u.name as customer_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.user_id";
        
        if ($status) {
            $query .= " WHERE o.status = :status";
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('orders/list', ['orders' => $orders, 'current_status' => $status]);
    }

    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=admin_orders");
            exit;
        }

        $stmt = $this->db->prepare("SELECT o.*, u.name as customer_name, u.gmail as customer_email 
                                    FROM orders o 
                                    LEFT JOIN users u ON o.user_id = u.user_id 
                                    WHERE o.order_id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('orders/detail', ['order' => $order, 'items' => $items]);
    }

    public function updateStatus()
    {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;
        if ($id && $status) {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->execute([$status, $id]);
        }
        header("Location: index.php?action=admin_orders");
        exit;
    }
}
