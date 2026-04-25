<?php

require_once __DIR__ . '/AdminBaseController.php';

class DashboardController extends AdminBaseController
{
    public function index()
    {
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0
        ];
        $this->render('dashboard', ['stats' => $stats]);
    }
}
