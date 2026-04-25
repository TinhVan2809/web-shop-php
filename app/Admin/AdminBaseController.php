<?php

require_once PROJECT_ROOT . '/app/Database.php';

class AdminBaseController
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        // Kiểm tra quyền truy cập (Optional - Bạn có thể bật lên sau)
        /*
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
            header("Location: index.php?action=login");
            exit;
        }
        */
    }

    protected function render($view, $data = [])
    {
        extract($data);
        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . "/views/admin/$view.php";
        include_once PROJECT_ROOT . '/components/admin_footer.php';
    }
}
