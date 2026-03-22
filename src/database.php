<?php
// src/Database.php
namespace Tinhl\Bai01QuanlySv; 

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;
    private $host = 'localhost';
    private $db_name = 'quanlysinhvien';
    private $username = 'root';
    private $password = '';
    private function __construct()
    {
        try {
            $this->conn = new

                PDO(
                    "mysql:host={$this->host};dbname={$this->db_name}",
                    $this->username,
                    $this->password
                );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,

                PDO::ERRMODE_EXCEPTION
            );

            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    public function getConnection()
    {
        return $this->conn;
    }
}
