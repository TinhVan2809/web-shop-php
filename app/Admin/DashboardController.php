<?php

require_once __DIR__ . '/AdminBaseController.php';

class DashboardController extends AdminBaseController
{
    public function index()
    {
        // 1. Lấy thống kê cơ bản cho các thẻ trên cùng
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0,
            'total_stock' => $this->db->query("SELECT SUM(quantity) FROM inventory")->fetchColumn() ?? 0,
            'out_of_stock' => $this->db->query("SELECT COUNT(*) FROM inventory WHERE available_quantity <= 0")->fetchColumn()
        ];

        // 2. Truy vấn lấy danh sách sản phẩm sắp hết hàng (available_quantity <= min_stock_level)
        $query = "SELECT p.name, pv.sku as variant_sku, p.sku as product_sku, 
                         i.available_quantity, i.min_stock_level, p.thumbnail, i.status, p.product_id
                  FROM inventory i
                  JOIN products p ON i.product_id = p.product_id
                  LEFT JOIN product_variants pv ON i.variant_id = pv.variant_id
                  WHERE i.available_quantity <= i.min_stock_level
                  ORDER BY i.available_quantity ASC
                  LIMIT 10";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Xử lý dữ liệu biểu đồ doanh thu 7 ngày gần nhất
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d/m', strtotime("-$i days"));
            $chartData[$date] = ['label' => $label, 'value' => 0];
        }

        $queryRev = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_total 
                     FROM orders 
                     WHERE status = 'completed' 
                     AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                     GROUP BY DATE(created_at)";
        $resRev = $this->db->query($queryRev)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resRev as $row) {
            if (isset($chartData[$row['order_date']])) {
                $chartData[$row['order_date']]['value'] = (float)$row['daily_total'];
            }
        }

        // 3.5. Xử lý dữ liệu sản phẩm bán chạy nhất trong 7 ngày qua
        $queryTop = "SELECT p.name, SUM(oi.quantity) as total_sold
                     FROM order_items oi
                     JOIN orders o ON oi.order_id = o.order_id
                     JOIN products p ON oi.product_id = p.product_id
                     WHERE o.status = 'completed'
                     AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                     GROUP BY p.product_id
                     ORDER BY total_sold DESC
                     LIMIT 10";
        $topProductsData = $this->db->query($queryTop)->fetchAll(PDO::FETCH_ASSOC);

        // 3.6. Thống kê trạng thái tồn kho cho biểu đồ
        $stockStatusQuery = "SELECT 
            COUNT(CASE WHEN available_quantity > min_stock_level THEN 1 END) as in_stock,
            COUNT(CASE WHEN available_quantity > 0 AND available_quantity <= min_stock_level THEN 1 END) as low_stock,
            COUNT(CASE WHEN available_quantity <= 0 THEN 1 END) as out_of_stock
            FROM inventory";
        $stockStatus = $this->db->query($stockStatusQuery)->fetch(PDO::FETCH_ASSOC);

        // 3.8. Thống kê tỷ lệ phần trăm các danh mục sản phẩm được bán ra
        $categorySalesQuery = "SELECT
            c.category_name,
            SUM(oi.quantity) as total_sold_quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN categories c ON p.category_id = c.category_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.status = 'completed'
        GROUP BY c.category_id, c.category_name
        ORDER BY total_sold_quantity DESC";

        $categorySalesData = $this->db->query($categorySalesQuery)->fetchAll(PDO::FETCH_ASSOC);
        $totalOverallSold = array_sum(array_column($categorySalesData, 'total_sold_quantity'));
        $categoryChartData = [];
        foreach ($categorySalesData as $data) {
            $percentage = ($totalOverallSold > 0) ? round(($data['total_sold_quantity'] / $totalOverallSold) * 100, 2) : 0;
            $categoryChartData[] = ['label' => $data['category_name'], 'value' => $percentage];
        }

        // 3.7. Lấy 5 đơn hàng mới nhất
        $latestOrdersQuery = "SELECT o.*, u.name as customer_name 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.user_id 
                             ORDER BY o.created_at DESC 
                             LIMIT 5";
        $latestOrders = $this->db->query($latestOrdersQuery)->fetchAll(PDO::FETCH_ASSOC);

        // 4. Render view dashboard với đầy đủ dữ liệu
        $this->render('dashboard', [
            'stats' => $stats,
            'lowStockProducts' => $lowStockProducts,
            'chartLabels' => array_column($chartData, 'label'),
            'chartValues' => array_column($chartData, 'value'),
            'topProductLabels' => array_column($topProductsData, 'name'),
            'topProductValues' => array_column($topProductsData, 'total_sold'),
            'stockStatus' => $stockStatus, // Existing stock status data
            'categoryChartLabels' => array_column($categoryChartData, 'label'), // New category chart labels
            'categoryChartValues' => array_column($categoryChartData, 'value'), // New category chart values
            'latestOrders' => $latestOrders // Existing latest orders data
        ]);
    }
}