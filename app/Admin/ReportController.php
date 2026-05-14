<?php

require_once PROJECT_ROOT . '/app/Database.php';

class ReportController
{
    private Database $db;
    private \PDO $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Báo cáo doanh thu theo ngày/tháng/năm
     */
    public function revenueReport()
    {
        $filter = $_GET['filter'] ?? 'month'; // day, month, year
        $date = $_GET['date'] ?? date('Y-m-d');
        
        if ($filter === 'day') {
            $query = "SELECT 
                        DATE_FORMAT(o.created_at, '%Y-%m-%d %H:00') as time_period,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_revenue,
                        AVG(o.total_amount) as avg_order_value
                      FROM orders o
                      WHERE DATE(o.created_at) = :date AND o.status != 'cancelled'
                      GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d %H:00')
                      ORDER BY o.created_at ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['date' => $date]);
        } elseif ($filter === 'month') {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $query = "SELECT 
                        DATE_FORMAT(o.created_at, '%Y-%m-%d') as time_period,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_revenue,
                        AVG(o.total_amount) as avg_order_value
                      FROM orders o
                      WHERE YEAR(o.created_at) = :year AND MONTH(o.created_at) = :month AND o.status != 'cancelled'
                      GROUP BY DATE(o.created_at)
                      ORDER BY o.created_at ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['year' => $year, 'month' => $month]);
        } else {
            $year = date('Y', strtotime($date));
            $query = "SELECT 
                        DATE_FORMAT(o.created_at, '%Y-%m') as time_period,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_revenue,
                        AVG(o.total_amount) as avg_order_value
                      FROM orders o
                      WHERE YEAR(o.created_at) = :year AND o.status != 'cancelled'
                      GROUP BY MONTH(o.created_at)
                      ORDER BY o.created_at ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['year' => $year]);
        }
        
        $revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tổng doanh thu
        $totalQuery = "SELECT 
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_revenue,
                        AVG(o.total_amount) as avg_order_value,
                        SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders
                      FROM orders o
                      WHERE o.status != 'cancelled'";
        $totalStmt = $this->conn->prepare($totalQuery);
        $totalStmt->execute();
        $summary = $totalStmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/reports/revenue_report.php';
    }

    /**
     * Thống kê sản phẩm bán chạy / tồn kho
     */
    public function productReport()
    {
        $type = $_GET['type'] ?? 'best_sellers'; // best_sellers, low_stock, top_revenue

        if ($type === 'best_sellers') {
            $query = "SELECT 
                        p.product_id,
                        p.name,
                        p.price,
                        p.thumbnail,
                        SUM(oi.quantity) as total_sold,
                        COUNT(DISTINCT oi.order_id) as num_orders,
                        SUM(oi.quantity * oi.price) as revenue
                      FROM products p
                      LEFT JOIN order_items oi ON p.product_id = oi.product_id
                      GROUP BY p.product_id
                      ORDER BY total_sold DESC
                      LIMIT 20";
        } elseif ($type === 'low_stock') {
            $query = "SELECT 
                        p.product_id,
                        p.name,
                        p.price,
                        p.thumbnail,
                        i.quantity as stock_quantity,
                        i.min_stock_level
                      FROM products p
                      LEFT JOIN inventory i ON p.product_id = i.product_id
                      WHERE i.quantity <= i.min_stock_level OR i.quantity < 5
                      ORDER BY i.quantity ASC";
        } else {
            $query = "SELECT 
                        p.product_id,
                        p.name,
                        p.price,
                        p.thumbnail,
                        SUM(oi.quantity) as total_sold,
                        SUM(oi.quantity * oi.price) as revenue
                      FROM products p
                      LEFT JOIN order_items oi ON p.product_id = oi.product_id
                      GROUP BY p.product_id
                      ORDER BY revenue DESC
                      LIMIT 20";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats
        $statsQuery = "SELECT 
                        COUNT(DISTINCT p.product_id) as total_products,
                        SUM(i.quantity) as total_stock,
                        COUNT(DISTINCT CASE WHEN i.quantity <= i.min_stock_level THEN p.product_id END) as low_stock_count
                      FROM products p
                      LEFT JOIN inventory i ON p.product_id = i.product_id";
        $statsStmt = $this->conn->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/reports/product_report.php';
    }

    /**
     * Thống kê đơn hàng theo trạng thái
     */
    public function orderReport()
    {
        $date_range = $_GET['date_range'] ?? '30'; // 7, 30, 90, all
        
        $whereClause = '';
        if ($date_range !== 'all') {
            $whereClause = " AND o.created_at >= DATE_SUB(NOW(), INTERVAL $date_range DAY)";
        }

        // Đơn hàng theo trạng thái
        $query = "SELECT 
                    o.status,
                    COUNT(o.order_id) as count,
                    SUM(o.total_amount) as revenue,
                    AVG(o.total_amount) as avg_value
                  FROM orders o
                  WHERE 1=1 $whereClause
                  GROUP BY o.status
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $order_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Chi tiết đơn hàng
        $detailQuery = "SELECT 
                        o.order_id,
                        o.order_code,
                        o.status,
                        o.payment_status,
                        o.total_amount,
                        o.recipient_name,
                        o.recipient_phone,
                        o.created_at
                      FROM orders o
                      WHERE 1=1 $whereClause
                      ORDER BY o.created_at DESC
                      LIMIT 50";
        $detailStmt = $this->conn->prepare($detailQuery);
        $detailStmt->execute();
        $orders = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

        // Tổng stats
        $summaryQuery = "SELECT 
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_revenue,
                        COUNT(DISTINCT CASE WHEN o.status = 'completed' THEN o.order_id END) as completed,
                        COUNT(DISTINCT CASE WHEN o.status = 'pending' THEN o.order_id END) as pending,
                        COUNT(DISTINCT CASE WHEN o.status = 'cancelled' THEN o.order_id END) as cancelled
                      FROM orders o
                      WHERE 1=1 $whereClause";
        $summaryStmt = $this->conn->prepare($summaryQuery);
        $summaryStmt->execute();
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/reports/order_report.php';
    }

    /**
     * Báo cáo khách hàng tiềm năng
     */
    public function customerReport()
    {
        $metric = $_GET['metric'] ?? 'top_buyers'; // top_buyers, loyal_customers, at_risk

        if ($metric === 'top_buyers') {
            $query = "SELECT 
                        u.user_id,
                        u.name,
                        u.gmail,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_spent,
                        AVG(o.total_amount) as avg_order_value,
                        MAX(o.created_at) as last_order_date
                      FROM users u
                      LEFT JOIN orders o ON u.user_id = o.user_id
                      WHERE u.role = 'customer'
                      GROUP BY u.user_id
                      ORDER BY total_spent DESC
                      LIMIT 30";
        } elseif ($metric === 'loyal_customers') {
            $query = "SELECT 
                        u.user_id,
                        u.name,
                        u.gmail,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_spent,
                        MAX(o.created_at) as last_order_date,
                        DATEDIFF(NOW(), MAX(o.created_at)) as days_since_last_order
                      FROM users u
                      LEFT JOIN orders o ON u.user_id = o.user_id
                      WHERE u.role = 'customer'
                      GROUP BY u.user_id
                      HAVING COUNT(o.order_id) >= 3
                      ORDER BY total_spent DESC
                      LIMIT 30";
        } else {
            $query = "SELECT 
                        u.user_id,
                        u.name,
                        u.gmail,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_spent,
                        MAX(o.created_at) as last_order_date,
                        DATEDIFF(NOW(), MAX(o.created_at)) as days_since_last_order
                      FROM users u
                      LEFT JOIN orders o ON u.user_id = o.user_id
                      WHERE u.role = 'customer'
                      GROUP BY u.user_id
                      HAVING days_since_last_order > 90
                      ORDER BY total_spent DESC
                      LIMIT 30";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Customer stats
        $statsQuery = "SELECT 
                        COUNT(DISTINCT u.user_id) as total_customers,
                        COUNT(DISTINCT CASE WHEN u.create_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN u.user_id END) as new_customers_30d,
                        SUM(o.total_amount) as total_revenue_all_time
                      FROM users u
                      LEFT JOIN orders o ON u.user_id = o.user_id
                      WHERE u.role = 'customer'";
        $statsStmt = $this->conn->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/reports/customer_report.php';
    }

    /**
     * Dashboard thống kê tổng hợp
     */
    public function dashboard()
    {
        // Total stats
        $statsQuery = "SELECT 
                        (SELECT COUNT(*) FROM users WHERE role = 'customer') as total_users,
                        (SELECT COUNT(*) FROM products) as total_products,
                        (SELECT COUNT(*) FROM orders) as total_orders,
                        (SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled') as total_revenue";
        $statsStmt = $this->conn->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Revenue for last 7 days
        $chartQuery = "SELECT 
                        DATE(o.created_at) as date,
                        SUM(o.total_amount) as revenue
                      FROM orders o
                      WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND o.status != 'cancelled'
                      GROUP BY DATE(o.created_at)
                      ORDER BY o.created_at ASC";
        $chartStmt = $this->conn->prepare($chartQuery);
        $chartStmt->execute();
        $chart_data = $chartStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Prepare chart data
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartLabels[] = date('d/m', strtotime("-$i days"));
            $found = false;
            foreach ($chart_data as $data) {
                if ($data['date'] === $date) {
                    $chartValues[] = $data['revenue'] ?: 0;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $chartValues[] = 0;
            }
        }

        // Low stock products
        $lowStockQuery = "SELECT 
                        p.product_id,
                        p.name,
                        p.thumbnail,
                        p.sku as product_sku,
                        i.available_quantity,
                        i.min_stock_level,
                        pv.sku as variant_sku
                      FROM products p
                      LEFT JOIN inventory i ON p.product_id = i.product_id
                      LEFT JOIN product_variants pv ON i.variant_id = pv.variant_id
                      WHERE i.available_quantity <= i.min_stock_level OR i.available_quantity = 0
                      ORDER BY i.available_quantity ASC
                      LIMIT 10";
        $lowStockStmt = $this->conn->prepare($lowStockQuery);
        $lowStockStmt->execute();
        $lowStockProducts = $lowStockStmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/dashboard.php';
    }
}
