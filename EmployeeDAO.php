<?php
// import
require_once "Database.php";
require_once "Employee.php";

class EmployeeDAO {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();  // this gets the singleton DB connection
    }
    
    public function __destruct() {
        $this->conn = null; // this ensures the database connection is closed when the object is destroyed
    }

/*
 getEmployeeById function fetches an employee's information, including department name,
 job position title, and employment status, by joining the `employee` table
 with the `department` and `job_position` tables. The employee ID is used
as a parameterized query to prevent SQL injection.
*/
    public function getEmployeeById($emp_id) {
        $sql = "SELECT e.*, d.dept_name, jp.position_title, e.employment_status 
                FROM employee e
                LEFT JOIN department d ON e.dept_id = d.dept_id
                LEFT JOIN job_position jp ON e.position_id = jp.position_id
                WHERE emp_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emp_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function updateEmployee($emp_id, $data) {
        $sql = "UPDATE employee 
                SET emp_name = ?, date_hired = ?, dept_id = ?, position_id = ?, 
                    designation = ?, employment_status = ?, hours_worked = ?, rate_per_hour = ?, 
                    ot_hours = ?, gross_pay = ?
                WHERE emp_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['emp_name'], $data['date_hired'], $data['dept_id'], $data['position_id'],
            $data['designation'], $data['employment_status'], $data['hours_worked'], $data['rate_per_hour'],
            $data['ot_hours'], $data['gross_pay'], $emp_id
        ]);
    }
    

    
    public function addEmployee($employee) {
        try {
            $details = $employee->getEmployeeDetails(); // saves the result so the function doesn’t have to run multiple times.
            
            // calculates the total earnings before deductions.
            $gross_pay = $employee->computeGrossPay(); 
    
            // verifies whether the Employee ID is already in the system.
            if ($this->employeeExists($details["emp_id"])) {
                echo "Error: Employee ID already exists.\n";
                return false;
            }
    
            // SQL query that retrieves data, including gross_pay.
            $sql = "INSERT INTO employee (emp_id, emp_name, date_hired, dept_id, position_id, designation, 
                                           employment_status, hours_worked, rate_per_hour, ot_hours, gross_pay) 
                    VALUES (:emp_id, :emp_name, :date_hired, :dept_id, :position_id, :designation, 
                            :employment_status, :hours_worked, :rate_per_hour, :ot_hours, :gross_pay)";
    
            // set up the SQL statement for execution.
            $stmt = $this->conn->prepare($sql);
    
            // runs the query with the appropriate values, including the calculated gross_pay.
            $stmt->execute([
                ':emp_id' => $details["emp_id"],
                ':emp_name' => $details["emp_name"],
                ':date_hired' => $details["date_hired"],
                ':dept_id' => $details["dept_id"],
                ':position_id' => $details["position_id"],
                ':designation' => $details["designation"],
                ':employment_status' => $details["employment_status"],
                ':hours_worked' => $details["hours_worked"],
                ':rate_per_hour' => $details["rate_per_hour"],
                ':ot_hours' => $details["ot_hours"],
                ':gross_pay' => $gross_pay // this will now storing gross pay in DB
            ]);
    
            echo "Employee added successfully!\n";
            return true;
        } catch (PDOException $e) {
            echo "Error: Unable to add employee.\n";
            error_log("Database Error (addEmployee): " . $e->getMessage());
            return false;
        }
    }
    
    

    public function listEmployees() {
        // fetches employees, now including gross_pay
        $sql = "SELECT e.emp_id, e.emp_name, e.date_hired, 
                       d.dept_name AS department_name, 
                       p.position_title AS position_name, 
                       e.designation, 
                       e.employment_status AS status_name, 
                       e.hours_worked, e.rate_per_hour, e.ot_hours,
                       e.gross_pay -- this starts selecting gross_pay from the database
                FROM employee e
                LEFT JOIN department d ON e.dept_id = d.dept_id
                LEFT JOIN job_position p ON e.position_id = p.position_id";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $employees; // no more manual computation needed
    }
    

    public function editEmployee($emp_id, $data) {
        $sql = "UPDATE employee 
                SET emp_name = :emp_name, 
                    date_hired = :date_hired, 
                    dept_id = :dept_id, 
                    position_id = :position_id, 
                    designation = :designation, 
                    employment_status = :employment_status, 
                    hours_worked = :hours_worked, 
                    rate_per_hour = :rate_per_hour, 
                    ot_hours = :ot_hours, 
                    gross_pay = :gross_pay 
                WHERE emp_id = :emp_id";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':emp_id' => $emp_id,
            ':emp_name' => $data['emp_name'],
            ':date_hired' => $data['date_hired'],
            ':dept_id' => $data['dept_id'],
            ':position_id' => $data['position_id'],
            ':designation' => $data['designation'],
            ':employment_status' => $data['employment_status'],
            ':hours_worked' => $data['hours_worked'],
            ':rate_per_hour' => $data['rate_per_hour'],
            ':ot_hours' => $data['ot_hours'],
            ':gross_pay' => $data['gross_pay']
        ]);
    }
    
    
    public function deleteEmployee($emp_id) {
        // SQL statements that deletes employee by ID
        $sql = "DELETE FROM employee WHERE emp_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        try {
            $stmt->execute([$emp_id]);
            if ($stmt->rowCount() > 0) {
                echo "Employee with ID $emp_id has been deleted.\n";
            } else {
                echo "Employee with ID $emp_id not found.\n";
            }
        } catch (PDOException $e) {
            echo "Error deleting employee: " . $e->getMessage() . "\n";
        }
    }

    // this function checks if an employee ID already exists
    public function employeeExists($emp_id) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM employee WHERE emp_id = ?");
            $stmt->execute([$emp_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking employee ID: " . $e->getMessage());
            return false;
        }
    }
}
?>
