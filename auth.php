<?php
// import
require_once "Database.php";

class Auth {
    private $conn;

    // consctructor
    public function __construct() {
        $database = new Database(); // makes database instance
        $this->conn = $database->conn; // stores database connection here
    }

    // login function
    public function login($username, $password) {
        // query to get user by name
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql); // prepare the query
        $stmt->execute([$username]); // executes with username
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // fetch the user data

        // if user is not found
        if (!$user) {
            echo "User not found!\n";
            return false;
        }

        // verifies if password is the same
        if (password_verify($password, $user['password_hash'])) {
            // query to get employee id from user id
            $emp_sql = "SELECT emp_id FROM employee WHERE user_id = ?";
            $emp_stmt = $this->conn->prepare($emp_sql); // prepares
            $emp_stmt->execute([$user['user_id']]); // executes with the inputted id
            $emp = $emp_stmt->fetch(PDO::FETCH_ASSOC); // fetch the employee data

            if ($emp) {
                // returns employee ID and role
                return [
                    'emp_id' => $emp['emp_id'], 
                    'role' => $user['role']
                ];
            } else {
                // no data found
                echo "Employee data not found!\n";
                return false;
            }
        } else {
            // invalid credentials
            echo "Invalid credentials!\n";
            return false;
        }
    }
}
?>
