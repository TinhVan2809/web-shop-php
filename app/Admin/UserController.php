<?php

require_once __DIR__ . '/AdminBaseController.php';

class UserController extends AdminBaseController
{
    public function list()
    {
        $query = "SELECT * FROM users ORDER BY create_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('users/list', ['users' => $users]);
    }

    public function form()
    {
        $id = $_GET['id'] ?? null;
        $user = null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $this->render('users/form', ['user' => $user]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? null;
            $name = $_POST['name'];
            $username = $_POST['username'];
            $role = $_POST['role'];
            $gmail = $_POST['gmail'];
            $number_phone = $_POST['number_phone'];
            $status = $_POST['status'];

            if ($id) {
                $query = "UPDATE users SET name=?, username=?, role=?, gmail=?, number_phone=?, status=? WHERE user_id=?";
                $params = [$name, $username, $role, $gmail, $number_phone, $status, $id];
                
                if (!empty($_POST['password'])) {
                    $query = "UPDATE users SET name=?, username=?, role=?, gmail=?, number_phone=?, status=?, password=? WHERE user_id=?";
                    $params = [$name, $username, $role, $gmail, $number_phone, $status, $_POST['password'], $id];
                }
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);
            } else {
                $password = $_POST['password'] ?: '123456';
                $query = "INSERT INTO users (name, username, role, gmail, number_phone, status, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$name, $username, $role, $gmail, $number_phone, $status, $password]);
            }
        }
        header("Location: index.php?action=admin_users");
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$id]);
        }
        header("Location: index.php?action=admin_users");
        exit;
    }

    public function toggleStatus()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT status FROM users WHERE user_id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetchColumn();
            $newStatus = ($current == 'active') ? 'locked' : 'active';
            
            $update = $this->db->prepare("UPDATE users SET status = ? WHERE user_id = ?");
            $update->execute([$newStatus, $id]);
        }
        header("Location: index.php?action=admin_users");
        exit;
    }
}
