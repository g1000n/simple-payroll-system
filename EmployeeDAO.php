<?php
require_once "Database.php";

class EmployeeDAO {
    private $conn;

    // constructor to set up the database connection
    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // cleanup the connection when done
    public function __destruct() {
        $this->conn = null;
    }

    // add an employee and create a user account
    public function addEmployee($name, $date_hired, $dept_id, $position_id, $designation, $status_id, $username, $password) {
        try {
            // hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // insert user into users table
            $sqlUser = "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([$username, $hashedPassword, 'user']);

            // get the user_id
            $user_id = $this->conn->lastInsertId();

            // insert employee into employee table
            $sqlEmployee = "INSERT INTO employee (emp_name, date_hired, dept_id, position_id, designation, status_id, user_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtEmployee = $this->conn->prepare($sqlEmployee);
            $stmtEmployee->execute([$name, $date_hired, $dept_id, $position_id, $designation, $status_id, $user_id]);

            return $this->conn->lastInsertId(); // return the new employee ID
        } catch (PDOException $e) {
            echo "Error adding employee: " . $e->getMessage() . "\n";
            return false;
        }
    }

    // delete employee and their user account
    public function deleteEmployee($emp_id) {
        try {
            // get user_id linked to the employee
            $sql = "SELECT user_id FROM employee WHERE emp_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$emp_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$employee) {
                echo "Error: Employee not found.\n";
                return false;
            }

            $user_id = $employee['user_id'];

            // delete employee
            $sql = "DELETE FROM employee WHERE emp_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$emp_id]);

            // delete user account if exists
            if ($user_id) {
                $sql = "DELETE FROM users WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$user_id]);
            }

            echo "Employee and linked user account deleted.\n";
            return true;
        } catch (PDOException $e) {
            echo "Error deleting employee: " . $e->getMessage() . "\n";
            return false;
        }
    }

    // get all departments
    public function getDepartments() {
        $sql = "SELECT * FROM department ORDER BY dept_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get all job positions
    public function getJobPositions() {
        $sql = "SELECT * FROM job_position ORDER BY position_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get all employment statuses
    public function getEmploymentStatuses() {
        $sql = "SELECT * FROM employment_status ORDER BY status_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // update employee details
    public function updateEmployee($emp_id, $name, $date_hired, $dept_id, $position_id, $designation, $status_id) {
        try {
            $sql = "UPDATE employee 
                    SET emp_name = ?, date_hired = ?, dept_id = ?, position_id = ?, designation = ?, status_id = ? 
                    WHERE emp_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $date_hired, $dept_id, $position_id, $designation, $status_id, $emp_id]);
        } catch (PDOException $e) {
            echo "Error updating employee: " . $e->getMessage() . "\n";
        }
    }

    // get all employees
    public function getAllEmployees() {
        $sql = "SELECT e.emp_id, e.emp_name, e.date_hired, 
                    d.dept_name, p.position_title, s.status_name 
                FROM employee e
                JOIN department d ON e.dept_id = d.dept_id
                JOIN job_position p ON e.position_id = p.position_id
                JOIN employment_status s ON e.status_id = s.status_id
                ORDER BY e.emp_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get employee by ID
    public function getEmployeeById($emp_id) {
        $sql = "SELECT * FROM employee WHERE emp_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emp_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // calculate and add payroll
    public function addPayroll($emp_id, $hours_worked, $rate_per_hour, $ot_hours) {
        try {
            // check if employee exists
            if (!$this->getEmployeeById($emp_id)) {
                echo "Error: Employee ID does not exist.\n";
                return false;
            }

            // calculate gross pay
            $gross_pay = ($hours_worked * $rate_per_hour) + ($ot_hours * $rate_per_hour * 1.3);

            // insert payroll
            $sql = "INSERT INTO payroll (emp_id, hours_worked, rate_per_hour, ot_hours, gross_pay) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$emp_id, $hours_worked, $rate_per_hour, $ot_hours, $gross_pay]);

            return true;
        } catch (PDOException $e) {
            echo "Error processing payroll: " . $e->getMessage() . "\n";
            return false;
        }
    }

    // get payroll history for all employees
    public function getPayrollHistory() {
        $sql = "SELECT p.payroll_id, p.emp_id, e.emp_name, p.hours_worked, p.rate_per_hour, 
                       p.ot_hours, p.gross_pay 
                FROM payroll p
                JOIN employee e ON p.emp_id = e.emp_id
                ORDER BY p.payroll_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get payroll for a specific employee
    public function getPayrollByEmpId($emp_id) {
        $sql = "SELECT p.payroll_id, p.emp_id, e.emp_name, p.hours_worked, p.rate_per_hour, 
                    p.ot_hours, p.gross_pay 
                FROM payroll p
                JOIN employee e ON p.emp_id = e.emp_id
                WHERE p.emp_id = ? 
                ORDER BY p.payroll_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
