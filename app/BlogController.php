<?php

require_once PROJECT_ROOT . '/app/Database.php';

class BlogController
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
     * Hiển thị danh sách blogs
     */
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Lấy tổng số blogs
        $countQuery = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute();
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalBlogs = $countResult['total'];
        $totalPages = ceil($totalBlogs / $perPage);

        // Lấy danh sách blogs
        $query = "SELECT * FROM blogs 
                  WHERE status = 'published' 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh mục
        $categoryQuery = "SELECT DISTINCT category FROM blogs WHERE status = 'published' ORDER BY category";
        $categoryStmt = $this->conn->prepare($categoryQuery);
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

        // Include header
        include_once PROJECT_ROOT . '/components/header.php';
        
        // Include view
        include_once PROJECT_ROOT . '/views/blogs.php';
    }

    /**
     * Hiển thị chi tiết một bài blog
     */
    public function detail()
    {
        $blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($blog_id === 0) {
            header('Location: index.php?action=blogs');
            exit;
        }

        // Tăng số lượt xem
        $updateViewsQuery = "UPDATE blogs SET views = views + 1 WHERE blog_id = :id";
        $viewsStmt = $this->conn->prepare($updateViewsQuery);
        $viewsStmt->execute(['id' => $blog_id]);

        // Lấy bài blog
        $query = "SELECT * FROM blogs WHERE blog_id = :id AND status = 'published'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $blog_id]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$blog) {
            header('Location: index.php?action=blogs');
            exit;
        }

        // Lấy bài blog liên quan (cùng danh mục)
        $relatedQuery = "SELECT * FROM blogs 
                        WHERE category = :category 
                        AND blog_id != :id 
                        AND status = 'published'
                        ORDER BY created_at DESC 
                        LIMIT 3";
        $relatedStmt = $this->conn->prepare($relatedQuery);
        $relatedStmt->execute(['category' => $blog['category'], 'id' => $blog_id]);
        $relatedBlogs = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

        // Include header
        include_once PROJECT_ROOT . '/components/header.php';
        
        // Include view
        include_once PROJECT_ROOT . '/views/blog_detail.php';
    }

    /**
     * Lọc blogs theo danh mục
     */
    public function filterByCategory()
    {
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Lấy tổng số blogs
        $countQuery = "SELECT COUNT(*) as total FROM blogs 
                      WHERE status = 'published' AND category = :category";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute(['category' => $category]);
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalBlogs = $countResult['total'];
        $totalPages = ceil($totalBlogs / $perPage);

        // Lấy danh sách blogs
        $query = "SELECT * FROM blogs 
                  WHERE status = 'published' AND category = :category
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh mục
        $categoryQuery = "SELECT DISTINCT category FROM blogs WHERE status = 'published' ORDER BY category";
        $categoryStmt = $this->conn->prepare($categoryQuery);
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

        // Include header
        include_once PROJECT_ROOT . '/components/header.php';
        
        // Include view
        include_once PROJECT_ROOT . '/views/blogs.php';
    }

    /**
     * Tìm kiếm blogs
     */
    public function search()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Lấy tổng số blogs tìm được
        $countQuery = "SELECT COUNT(*) as total FROM blogs 
                      WHERE status = 'published' 
                      AND (title LIKE :keyword OR content LIKE :keyword OR excerpt LIKE :keyword)";
        $countStmt = $this->conn->prepare($countQuery);
        $searchKeyword = '%' . $keyword . '%';
        $countStmt->execute(['keyword' => $searchKeyword]);
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalBlogs = $countResult['total'];
        $totalPages = ceil($totalBlogs / $perPage);

        // Lấy danh sách blogs
        $query = "SELECT * FROM blogs 
                  WHERE status = 'published'
                  AND (title LIKE :keyword OR content LIKE :keyword OR excerpt LIKE :keyword)
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':keyword', $searchKeyword);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh mục
        $categoryQuery = "SELECT DISTINCT category FROM blogs WHERE status = 'published' ORDER BY category";
        $categoryStmt = $this->conn->prepare($categoryQuery);
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

        // Include header
        include_once PROJECT_ROOT . '/components/header.php';
        
        // Include view
        include_once PROJECT_ROOT . '/views/blogs.php';
    }
}
