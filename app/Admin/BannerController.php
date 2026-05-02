<?php

require_once __DIR__ . '/AdminBaseController.php';

class BannerController extends AdminBaseController
{
    public function list()
    {
        $query = "SELECT * FROM banner ORDER BY create_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('banner/list', ['banners' => $banners]);
    }

    public function form()
    {
        $id = $_GET['id'] ?? null;
        $banner = null;

        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM banner WHERE banenr_id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $this->render('banner/form', ['banner' => $banner]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['banenr_id'] ?? null;
            $content = $_POST['content'];
            $url = $_POST['url'] ?? '';
            $img = $_POST['current_img'];

            // Xử lý upload ảnh mới nếu có
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $filename = time() . '_banner_' . $_FILES['img']['name'];
                if (move_uploaded_file($_FILES['img']['tmp_name'], PROJECT_ROOT . '/asset/' . $filename)) {
                    // Xóa ảnh cũ nếu là cập nhật
                    if ($id && $img && file_exists(PROJECT_ROOT . '/asset/' . $img)) {
                        unlink(PROJECT_ROOT . '/asset/' . $img);
                    }
                    $img = $filename;
                }
            }

            if ($id) {
                $query = "UPDATE banner SET img = ?, content = ?, url = ? WHERE banenr_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$img, $content, $url, $id]);
            } else {
                $query = "INSERT INTO banner (img, content, url) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$img, $content, $url]);
            }
        }
        header("Location: index.php?action=admin_banners");
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Lấy thông tin ảnh để xóa file vật lý
            $stmt = $this->db->prepare("SELECT img FROM banner WHERE banenr_id = ?");
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();

            if ($img && file_exists(PROJECT_ROOT . '/asset/' . $img)) {
                unlink(PROJECT_ROOT . '/asset/' . $img);
            }

            $stmt = $this->db->prepare("DELETE FROM banner WHERE banenr_id = ?");
            $stmt->execute([$id]);
        }
        header("Location: index.php?action=admin_banners");
        exit;
    }
}