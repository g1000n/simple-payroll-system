<?php
require_once "EmployeeDAO.php";

// adds employee
function addEmployee($employeeDAO) {
    echo "Enter Employee Name: ";
    $name = trim(fgets(STDIN));

    echo "Enter Date Hired (YYYY-MM-DD): ";
    $date_hired = trim(fgets(STDIN));

    // retrieves and validates department ID
    do {
        echo "\nAvailable Departments:\n";
        $departments = $employeeDAO->getDepartments();
        foreach ($departments as $dept) {
            echo "{$dept['dept_id']}. {$dept['dept_name']}\n";
        }
        echo "Enter Department ID: ";
        $dept_id = trim(fgets(STDIN));

        $validDept = array_filter($departments, fn($d) => $d['dept_id'] == $dept_id);
        if (!$validDept) echo "Invalid Department ID. Please enter a valid one.\n";
    } while (!$validDept);

    // retrieves and and validates Position ID 
    do {
        echo "\nAvailable Job Positions:\n";
        $positions = $employeeDAO->getJobPositions();
        foreach ($positions as $job) {
            echo "{$job['position_id']}. {$job['position_title']}\n";
        }
        echo "Enter Position ID: ";
        $position_id = trim(fgets(STDIN));

        $validPosition = array_filter($positions, fn($p) => $p['position_id'] == $position_id);
        if (!$validPosition) echo "Invalid Position ID. Please enter a valid one.\n";
    } while (!$validPosition);

    echo "Enter Designation: ";
    $designation = trim(fgets(STDIN));

    // retrieves and validates Employment Status ID
    do {
        echo "\nAvailable Employment Statuses:\n";
        $statuses = $employeeDAO->getEmploymentStatuses();
        foreach ($statuses as $status) {
            echo "{$status['status_id']}. {$status['status_name']}\n";
        }
        echo "Enter Status ID: ";
        $status_id = trim(fgets(STDIN));

        $validStatus = array_filter($statuses, fn($s) => $s['status_id'] == $status_id);
        if (!$validStatus) echo "Invalid Status ID. Please enter a valid one.\n";
    } while (!$validStatus);

    // retrieves username and password
    echo "Enter Username for Employee: ";
    $username = trim(fgets(STDIN));

    echo "Enter Password for Employee: ";
    $password = trim(fgets(STDIN));

    // inserts employee and user
    $emp_id = $employeeDAO->addEmployee($name, $date_hired, $dept_id, $position_id, $designation, $status_id, $username, $password);
    if (!$emp_id) {
        echo "Error adding employee.\n";
        return;
    }

    echo "Employee added successfully with Username: $username\n";
}





// views employees
function viewEmployees($employeeDAO) {
    echo "\n=== Employee List ===\n";
    foreach ($employeeDAO->getAllEmployees() as $emp) {
        echo "{$emp['emp_id']}: {$emp['emp_name']} - Hired: {$emp['date_hired']} - ";
        echo "Dept: {$emp['dept_name']} - Position: {$emp['position_title']} - Status: {$emp['status_name']}\n";
    }
}

// edits employees
function editEmployee($employeeDAO) {
    echo "Enter Employee ID to edit: ";
    $emp_id = trim(fgets(STDIN));
    $employee = $employeeDAO->getEmployeeById($emp_id);

    if (!$employee) {
        echo "Employee not found!\n";
        return;
    }

    echo "Editing Employee: {$employee['emp_name']}\n";
    
    echo "Enter new Employee Name ({$employee['emp_name']}): ";
    $name = trim(fgets(STDIN)) ?: $employee['emp_name'];

    echo "Enter new Date Hired ({$employee['date_hired']}): ";
    $date_hired = trim(fgets(STDIN)) ?: $employee['date_hired'];

    displayDepartments($employeeDAO);
    echo "Enter new Department ID ({$employee['dept_id']}): ";
    $dept_id = trim(fgets(STDIN)) ?: $employee['dept_id'];

    displayJobPositions($employeeDAO);
    echo "Enter new Position ID ({$employee['position_id']}): ";
    $position_id = trim(fgets(STDIN)) ?: $employee['position_id'];

    echo "Enter new Designation ({$employee['designation']}): ";
    $designation = trim(fgets(STDIN)) ?: $employee['designation'];

    displayEmploymentStatuses($employeeDAO);
    echo "Enter new Status ID ({$employee['status_id']}): ";
    $status_id = trim(fgets(STDIN)) ?: $employee['status_id'];

    $employeeDAO->updateEmployee($emp_id, $name, $date_hired, $dept_id, $position_id, $designation, $status_id);
    echo "Employee updated successfully!\n";
}

// deletes employees
function deleteEmployee($employeeDAO) {
    echo "Enter Employee ID to delete: ";
    $emp_id = trim(fgets(STDIN));

    $employee = $employeeDAO->getEmployeeById($emp_id);
    if ($employee) {
        echo "Are you sure you want to delete {$employee['emp_name']}? (yes/no): ";
        if (strtolower(trim(fgets(STDIN))) === 'yes') {
            $employeeDAO->deleteEmployee($emp_id);
            echo "Employee deleted successfully!\n";
        } else {
            echo "Deletion canceled.\n";
        }
    } else {
        echo "Employee ID not found!\n";
    }
}

// helper function that displays departments
function displayDepartments($employeeDAO) {
    echo "\nAvailable Departments:\n";
    foreach ($employeeDAO->getDepartments() as $dept) {
        echo "{$dept['dept_id']}. {$dept['dept_name']}\n";
    }
}

// helper function that displays job positions
function displayJobPositions($employeeDAO) {
    echo "\nAvailable Job Positions:\n";
    foreach ($employeeDAO->getJobPositions() as $job) {
        echo "{$job['position_id']}. {$job['position_title']}\n";
    }
}

// helper function that displays employee statuses
function displayEmploymentStatuses($employeeDAO) {
    echo "\nAvailable Employment Statuses:\n";
    foreach ($employeeDAO->getEmploymentStatuses() as $status) {
        echo "{$status['status_id']}. {$status['status_name']}\n";
    }
}

// payroll functions

// adds payroll
function addPayroll($employeeDAO) {
    echo "Enter Employee ID: ";
    $emp_id = trim(fgets(STDIN));

    // validates if employee exists
    $employee = $employeeDAO->getEmployeeById($emp_id);
    if (!$employee) {
        echo "Error: Employee ID does not exist.\n";
        return;
    }

    echo "Enter Hourly Rate: ";
    $rate_per_hour = floatval(trim(fgets(STDIN)));

    echo "Enter Hours Worked: ";
    $hours_worked = floatval(trim(fgets(STDIN)));

    echo "Enter Overtime Hours: ";
    $ot_hours = floatval(trim(fgets(STDIN)));

    // inserts into payroll table
    if ($employeeDAO->addPayroll($emp_id, $hours_worked, $rate_per_hour, $ot_hours)) {
        echo "Payroll record added successfully!\n";
    } else {
        echo "Error adding payroll record.\n";
    }
}

// views payroll history (only accessible for admin)
function viewPayrollHistory($employeeDAO) {
    $payrolls = $employeeDAO->getPayrollHistory();

    if (!$payrolls) {
        echo "No payroll records found.\n";
        return;
    }

    echo str_pad("ID", 5) . str_pad("Emp ID", 8) . str_pad("Employee", 20) . 
         str_pad("Hours", 10) . str_pad("Rate", 10) . 
         str_pad("OT Hours", 10) . str_pad("Gross Pay", 12) . "\n";

    echo str_repeat("-", 80) . "\n";

    foreach ($payrolls as $p) {
        echo str_pad($p['payroll_id'], 5) .
             str_pad($p['emp_id'], 8) .
             str_pad($p['emp_name'], 20) .
             str_pad($p['hours_worked'], 10) .
             str_pad($p['rate_per_hour'], 10) .
             str_pad($p['ot_hours'], 10) .
             str_pad($p['gross_pay'], 12) . "\n";
    }
}

// function to view employee's respective payroll
function viewOwnPayroll($employeeDAO, $emp_id) {
    $payrolls = $employeeDAO->getPayrollByEmpId($emp_id);

    if (!$payrolls) {
        echo "No payroll records found.\n";
        return;
    }

    echo str_pad("ID", 5) . str_pad("Emp ID", 8) . str_pad("Employee", 20) . 
         str_pad("Hours", 10) . str_pad("Rate", 10) . 
         str_pad("OT Hours", 10) . str_pad("Gross Pay", 12) . "\n";

    echo str_repeat("-", 80) . "\n";

    foreach ($payrolls as $p) {
        echo str_pad($p['payroll_id'], 5).
             str_pad($p['emp_id'], 8).
             str_pad($p['emp_name'], 20).
             str_pad($p['hours_worked'], 10).
             str_pad($p['rate_per_hour'], 10).
             str_pad($p['ot_hours'], 10).
             str_pad($p['gross_pay'], 12) . "\n";
    }
}

?>
