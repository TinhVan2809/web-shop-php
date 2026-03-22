<?php

namespace Tinhl\Bai01QuanlySv\models;

use Tinhl\Bai01QuanlySv\Database;
use PDO;

class StudentModel
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    // Thêm sinh viên mới
    public function addStudent($name, $email, $phone)
    {
        $stmt = $this->conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");

        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Nâng cấp hàm getAllStudents để có thể tìm kiếm
    public function getAllStudents($keyword = null)
    {
        // Bắt đầu câu lệnh SQL
        $sql = "SELECT * FROM students";
        // Nếu có từ khóa tìm kiếm, thêm điều kiện WHERE
        if ($keyword) {
            // Sử dụng LIKE để tìm kiếm gần đúng
            $sql .= " WHERE name LIKE :keyword";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        // Nếu có từ khóa, gán giá trị cho tham số :keyword
        if ($keyword) {
            // Thêm dấu % vào hai bên từ khóa để tìm kiếm bất kỳ


            $searchKeyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchKeyword);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
