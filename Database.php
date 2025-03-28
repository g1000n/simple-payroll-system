<?php
class Database {
    // stores the single instance of the Database class
    private static $instance = null;
    private $conn;

    // database connection details
    private $host = "localhost";
    private $dbname = "payroll_system";
    private $user = "root";
    private $pass = "";

    // private constructor to prevent creating multiple connections
    private function __construct() {
        try {
            // connect to the database using PDO
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);
            // enable error reporting for easier debugging
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // stop the program if the connection fails and show an error message
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    // returns the single instance of the Database class (Singleton Pattern)
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // returns the database connection
    public function getConnection() {
        return $this->conn;
    }
}

?>
