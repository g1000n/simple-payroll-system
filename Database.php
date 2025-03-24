<?php
class Database {
    private $conn;

    // Database configuration directly inside this file
    private $host = "localhost";
    private $dbname = "payroll_system";
    private $user = "root";
    private $pass = "";

    public function connect() {
        try {
            // Use the database credentials from within the class
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
}
?>
