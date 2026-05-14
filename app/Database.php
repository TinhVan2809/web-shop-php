<?php

class Database {
    private $host = "localhost";
    private $db_name = "web_shop_php";
    private $username = "root";
    private $password = "";
    public ?PDO $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->conn->exec("SET NAMES utf8mb4");
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            $this->conn->exec("SET character_set_connection=utf8mb4");
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}