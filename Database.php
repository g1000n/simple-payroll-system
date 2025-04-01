<?php
class Database {
    private $host = "localhost";
    private $dbname = "payroll_system";
    private $username = "root";
    private $password = "";
    public $conn;

    // set up the connection to the database
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Connection failed: " . $exception->getMessage());
        }
    }
}
?>
