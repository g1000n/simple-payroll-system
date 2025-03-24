<?php
// import
require_once "Database.php";
require_once "Employee.php";

class EmployeeDAO {
    private $conn;

    public function __construct() {
        $database = new Database();  // Create a new Database instance to be able to run the class
        $this->conn = $database->connect();  // Establish a database connection
    }
    

    public function addEmployee($employee) {
        // sql statement for the xamp
        $sql = "INSERT INTO employees (emp_id, emp_name, date_hired, dept, position, designation, 
                                       employment_status, hours_worked, rate_per_hour, ot_hours, gross_pay) 
                VALUES (:emp_id, :emp_name, :date_hired, :dept, :position, :designation, 
                        :employment_status, :hours_worked, :rate_per_hour, :ot_hours, :gross_pay)";
        // prepares sql to be sent values
        $stmt = $this->conn->prepare($sql);
        // sends an array of values using getemployee details from employee class and identifying the needed values with the keys
        $stmt->execute([
            ':emp_id' => $employee->getEmployeeDetails()["ID"],
            ':emp_name' => $employee->getEmployeeDetails()["Name"],
            ':date_hired' => $employee->getEmployeeDetails()["Date Hired"],
            ':dept' => $employee->getEmployeeDetails()["Department"],
            ':position' => $employee->getEmployeeDetails()["Position"],
            ':designation' => $employee->getEmployeeDetails()["Designation"],
            ':employment_status' => $employee->getEmployeeDetails()["Employment Status"],
            ':hours_worked' => $employee->getEmployeeDetails()["Hours Worked"],
            ':rate_per_hour' => $employee->getEmployeeDetails()["Rate Per Hour"],
            ':ot_hours' => $employee->getEmployeeDetails()["OT Hours"],
            ':gross_pay' => $employee->computeGrossPay()
        ]);
        echo "Employee added successfully!\n";
    }
    

    public function listEmployees() {
        // sql statement
        $sql = "SELECT emp_id, emp_name, date_hired, dept, position, designation, 
                       employment_status, hours_worked, rate_per_hour, ot_hours, gross_pay 
                FROM employees";
        // ready and executes
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch and return all results as an associative array
    }

    public function editEmployee($emp_id, $updatedFields) {
        $sql = "UPDATE employees SET 
                    emp_name = :emp_name,
                    date_hired = :date_hired,
                    dept = :dept,
                    position = :position,
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
            ':emp_name' => $updatedFields['emp_name'],
            ':date_hired' => $updatedFields['date_hired'],
            ':dept' => $updatedFields['dept'],
            ':position' => $updatedFields['position'],
            ':designation' => $updatedFields['designation'],
            ':employment_status' => $updatedFields['employment_status'],
            ':hours_worked' => $updatedFields['hours_worked'],
            ':rate_per_hour' => $updatedFields['rate_per_hour'],
            ':ot_hours' => $updatedFields['ot_hours'],
            ':gross_pay' => $updatedFields['gross_pay']
        ]);
    
        echo "Employee record updated successfully!\n";
    }
    
    
    public function deleteEmployee($emp_id) {
        $sql = "DELETE FROM employees WHERE emp_id = ?";
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
    
}
?>
