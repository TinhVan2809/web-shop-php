<?php
// src/Models/UserModel.php
namespace Tinhl\Bai01QuanlySv\Models;

use Tinhl\Bai01QuanlySv\Database;
use PDO;

class UserModel
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    /**
     * Tìm người dùng bằng username
     */
    public function findUserByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");

        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Tạo người dùng mới
     */
    public function createUser($name, $username, $password)
    {
        // Kiểm tra xem username đã tồn tại chưa
        if ($this->findUserByUsername($username)) {
            return false; // Username đã tồn tại
        }
        // --- Băm mật khẩu - BƯỚC BẢO MẬT QUAN TRỌNG NHẤT ---
        $passwordHash = password_hash(
            $password,

            PASSWORD_DEFAULT
        );

        $stmt = $this->conn->prepare(

            "INSERT INTO users (name, username, password) VALUES

(:name, :username, :password)"

        );
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $passwordHash);
        return $stmt->execute();
    }
}
