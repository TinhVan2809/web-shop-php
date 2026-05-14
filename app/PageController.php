<?php

require_once PROJECT_ROOT . '/app/Database.php';

class PageController
{
    private Database $db;
    private \PDO $conn;

    public function __construct()
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Danh sách pages
     */
    public function list()
    {
        $query = "SELECT * FROM pages ORDER BY position ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/cms/page_list.php';
    }

    /**
     * Form thêm/sửa page
     */
    public function form()
    {
        $page = null;
        $page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($page_id > 0) {
            $query = "SELECT * FROM pages WHERE page_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $page_id]);
            $page = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/cms/page_form.php';
    }

    /**
     * Lưu page
     */
    public function save()
    {
        $page_id = isset($_POST['page_id']) ? (int)$_POST['page_id'] : 0;
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (empty($title)) {
            $_SESSION['error'] = 'Tiêu đề không được để trống';
            header('Location: index.php?action=page_admin_form');
            exit;
        }

        // Auto-generate slug
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        if ($page_id > 0) {
            // Update
            $query = "UPDATE pages SET 
                      title = :title, 
                      slug = :slug, 
                      content = :content, 
                      meta_description = :meta_description,
                      position = :position,
                      is_published = :is_published,
                      updated_at = NOW()
                      WHERE page_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':content' => $content,
                ':meta_description' => $meta_description,
                ':position' => $position,
                ':is_published' => $is_published,
                ':id' => $page_id
            ]);
            $_SESSION['success'] = 'Cập nhật trang thành công!';
        } else {
            // Insert
            $query = "INSERT INTO pages (title, slug, content, meta_description, position, is_published) 
                      VALUES (:title, :slug, :content, :meta_description, :position, :is_published)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':content' => $content,
                ':meta_description' => $meta_description,
                ':position' => $position,
                ':is_published' => $is_published
            ]);
            $_SESSION['success'] = 'Tạo trang thành công!';
        }

        header('Location: index.php?action=admin_pages');
        exit;
    }

    /**
     * Xóa page
     */
    public function delete()
    {
        $page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($page_id > 0) {
            $query = "DELETE FROM pages WHERE page_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $page_id]);
            $_SESSION['success'] = 'Xóa trang thành công!';
        }

        header('Location: index.php?action=admin_pages');
        exit;
    }

    /**
     * Xem trang
     */
    public function view()
    {
        $slug = $_GET['slug'] ?? '';

        if (empty($slug)) {
            header('Location: index.php');
            exit;
        }

        $query = "SELECT * FROM pages WHERE slug = :slug AND is_published = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['slug' => $slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page) {
            header('Location: index.php');
            exit;
        }

        include_once PROJECT_ROOT . '/components/header.php';
        include_once PROJECT_ROOT . '/views/page_view.php';
    }
}
