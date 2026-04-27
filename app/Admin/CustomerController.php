<?php

require_once __DIR__ . '/AdminBaseController.php';

class CustomerController extends AdminBaseController
{
    public function list()
    {
        $search = trim($_GET['search'] ?? '');
        $type   = $_GET['type'] ?? 'all'; // all | new | frequent | vip

        // ── Global stats ────────────────────────────────────────────────
        $total_customers = $this->db
            ->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")
            ->fetchColumn();

        $new_customers = $this->db
            ->query("SELECT COUNT(*) FROM users WHERE role = 'customer'
                      AND create_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")
            ->fetchColumn();

        $frequent_customers = $this->db
            ->query("SELECT COUNT(*) FROM (
                         SELECT user_id FROM orders
                         GROUP BY user_id HAVING COUNT(*) >= 2
                     ) t")
            ->fetchColumn();

        $total_revenue = $this->db
            ->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders
                      WHERE payment_status = 'paid'")
            ->fetchColumn();

        // ── Build customer query with filters ───────────────────────────
        $where  = "WHERE u.role = 'customer'";
        $params = [];

        if ($search !== '') {
            $where .= " AND (u.name LIKE :search
                          OR u.gmail LIKE :search
                          OR u.username LIKE :search
                          OR CAST(u.number_phone AS CHAR) LIKE :search)";
            $params['search'] = "%$search%";
        }

        if ($type === 'new') {
            $where .= " AND u.create_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        $having = '';
        if ($type === 'frequent') {
            $having = 'HAVING order_count >= 2 AND order_count < 5';
        } elseif ($type === 'vip') {
            $having = 'HAVING order_count >= 5';
        }

        $query = "SELECT
                    u.user_id, u.name, u.username, u.gmail,
                    u.number_phone, u.status, u.avatar, u.create_at,
                    COUNT(DISTINCT o.order_id)          AS order_count,
                    COALESCE(SUM(o.total_amount), 0)    AS total_spent,
                    COALESCE(SUM(CASE WHEN o.payment_status = 'paid' THEN o.total_amount ELSE 0 END), 0) AS paid_amount,
                    MAX(o.created_at)                   AS last_order_date
                  FROM users u
                  LEFT JOIN orders o ON u.user_id = o.user_id
                  $where
                  GROUP BY u.user_id
                  $having
                  ORDER BY u.create_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [
            'total'         => $total_customers,
            'new'           => $new_customers,
            'frequent'      => $frequent_customers,
            'total_revenue' => $total_revenue,
        ];

        $this->render('customers/list', [
            'customers' => $customers,
            'stats'     => $stats,
            'search'    => $search,
            'type'      => $type,
        ]);
    }

    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=admin_customers");
            exit;
        }

        // Customer info
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE user_id = ? AND role = 'customer'"
        );
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            header("Location: index.php?action=admin_customers");
            exit;
        }

        // Order history with item count
        $stmt = $this->db->prepare(
            "SELECT o.*,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.order_id) AS item_count
             FROM orders o
             WHERE o.user_id = ?
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([$id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Per-customer stats
        $order_count  = count($orders);
        $total_spent  = array_sum(array_column($orders, 'total_amount'));
        $paid_orders  = array_filter($orders, fn($o) => $o['payment_status'] === 'paid');
        $paid_amount  = array_sum(array_column($paid_orders, 'total_amount'));
        $avg_order    = $order_count > 0 ? $total_spent / $order_count : 0;

        $this->render('customers/detail', [
            'customer'    => $customer,
            'orders'      => $orders,
            'order_count' => $order_count,
            'total_spent' => $total_spent,
            'paid_amount' => $paid_amount,
            'avg_order'   => $avg_order,
        ]);
    }
}
