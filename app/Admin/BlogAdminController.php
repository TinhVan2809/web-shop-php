<?php

require_once PROJECT_ROOT . '/app/Database.php';

class BlogAdminController
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
     * Danh sách blogs
     */
    public function list()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Tổng số blogs
        $countQuery = "SELECT COUNT(*) as total FROM blogs";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute();
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalBlogs = $countResult['total'];
        $totalPages = ceil($totalBlogs / $perPage);

        // Danh sách blogs
        $query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/cms/blog_list.php';
    }

    /**
     * Form thêm/sửa blog
     */
    public function form()
    {
        $blog = null;
        $blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($blog_id > 0) {
            $query = "SELECT * FROM blogs WHERE blog_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $blog_id]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        include_once PROJECT_ROOT . '/components/admin_header.php';
        include_once PROJECT_ROOT . '/views/admin/cms/blog_form.php';
    }

    /**
     * Lưu blog
     */
    public function save()
    {
        $blog_id = isset($_POST['blog_id']) ? (int)$_POST['blog_id'] : 0;
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $excerpt = $_POST['excerpt'] ?? '';
        $category = $_POST['category'] ?? 'General';
        $author = $_POST['author'] ?? 'Admin';
        $status = $_POST['status'] ?? 'draft';
        $thumbnail = $_POST['thumbnail'] ?? '';

        if (empty($title)) {
            $_SESSION['error'] = 'Tiêu đề không được để trống';
            header('Location: index.php?action=blog_admin_form');
            exit;
        }

        // Auto-generate slug
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        // Handle thumbnail upload
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
            $file = $_FILES['thumbnail'];
            $filename = time() . '_' . basename($file['name']);
            $target_path = PROJECT_ROOT . '/asset/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $thumbnail = $filename;
            }
        }

        if ($blog_id > 0) {
            // If no new thumbnail uploaded, keep the old one
            if (empty($thumbnail)) {
                $query = "SELECT thumbnail FROM blogs WHERE blog_id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->execute(['id' => $blog_id]);
                $old_blog = $stmt->fetch(PDO::FETCH_ASSOC);
                $thumbnail = $old_blog['thumbnail'] ?? '';
            }

            // Update
            $query = "UPDATE blogs SET 
                      title = :title, 
                      slug = :slug, 
                      content = :content, 
                      excerpt = :excerpt, 
                      category = :category,
                      author = :author,
                      status = :status,
                      thumbnail = :thumbnail,
                      updated_at = NOW()
                      WHERE blog_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':content' => $content,
                ':excerpt' => $excerpt,
                ':category' => $category,
                ':author' => $author,
                ':status' => $status,
                ':thumbnail' => $thumbnail,
                ':id' => $blog_id
            ]);
            $_SESSION['success'] = 'Cập nhật bài viết thành công!';
        } else {
            // Insert
            $query = "INSERT INTO blogs (title, slug, content, excerpt, category, author, status, thumbnail, views) 
                      VALUES (:title, :slug, :content, :excerpt, :category, :author, :status, :thumbnail, 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':content' => $content,
                ':excerpt' => $excerpt,
                ':category' => $category,
                ':author' => $author,
                ':status' => $status,
                ':thumbnail' => $thumbnail
            ]);
            $_SESSION['success'] = 'Tạo bài viết thành công!';
        }

        header('Location: index.php?action=admin_blogs');
        exit;
    }

    /**
     * Xóa blog
     */
    public function delete()
    {
        $blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($blog_id > 0) {
            $query = "DELETE FROM blogs WHERE blog_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $blog_id]);
            $_SESSION['success'] = 'Xóa bài viết thành công!';
        }

        header('Location: index.php?action=admin_blogs');
        exit;
    }

    /**
     * Thay đổi trạng thái
     */
    public function changeStatus()
    {
        $blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : 'draft';

        if ($blog_id > 0 && in_array($status, ['draft', 'published', 'archived'])) {
            $query = "UPDATE blogs SET status = :status WHERE blog_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['status' => $status, 'id' => $blog_id]);
            $_SESSION['success'] = 'Thay đổi trạng thái thành công!';
        }

        header('Location: index.php?action=admin_blogs');
        exit;
    }
}
