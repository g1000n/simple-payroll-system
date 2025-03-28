<?php
require_once 'choice.php';

function selectOption($message, $options) {
    echo "\n$message\n";
    foreach ($options as $key => $value) {
        echo "$key. $value\n"; // Ensure key is numeric
    }
    echo "Enter Choice: ";
    $choice = trim(fgets(STDIN));

    if (isset($options[$choice])) {
        return $choice; // Return correct ID (numeric)
    } else {
        echo "Invalid selection.\n";
        return null;
    }
}


// adding employee
function addEmployee($employeeDAO, $departments, $positions, $statuses) {
    echo "Enter Employee Name: ";
    $emp_name = trim(fgets(STDIN));
    echo "Enter Date Hired (YYYY-MM-DD): ";
    $date_hired = trim(fgets(STDIN));

    // get IDs
    $dept_id = selectOption("Select Department:", $departments);
    $position_id = selectOption("Select Position:", $positions);    
    $status_id = selectOption("Select Employment Status:", $statuses);
    echo "Enter Designation: ";
    $designation = trim(fgets(STDIN));
    echo "Enter Hours Worked: ";
    $hours_worked = (float) trim(fgets(STDIN));
    echo "Enter Rate per Hour: ";
    $rate_per_hour = (float) trim(fgets(STDIN));
    echo "Enter Overtime Hours: ";
    $ot_hours = (float) trim(fgets(STDIN));

    // make employee object
    $employee = new Employee(
        null, // id is auto generated
        $emp_name, $date_hired, $dept_id,
        $position_id, $designation, $status_id,
        $hours_worked, $rate_per_hour, $ot_hours
    );


    // compute gross pay from employee class
    $gross_pay = $employee->computeGrossPay(); 

    // calls addEmployee from DAO
    $employeeDAO->addEmployee($employee, $gross_pay);
}


// view function
function viewEmployees($employeeDAO) {
    $employees = $employeeDAO->listEmployees(); // gets employee list from DAO

    if (empty($employees)) {
        echo "\nNo employees found.\n";
        return;
    }

    echo "\n=== Employee Records ===\n";
    echo str_pad("ID", 6) . str_pad("Name", 20) . str_pad("Date Hired", 15) . str_pad("Dept", 25) .
         str_pad("Position", 15) . str_pad("Designation", 15) . str_pad("Status", 15) .
         str_pad("Hours", 8) . str_pad("Rate", 8) . str_pad("OT", 8) . str_pad("Gross Pay", 12) . "\n";
    echo str_repeat("-", 140) . "\n";

    foreach ($employees as $emp) {
        echo str_pad($emp['emp_id'], 6) .
             str_pad($emp['emp_name'], 20) .
             str_pad($emp['date_hired'], 15) .
             str_pad($emp['department_name'], 25) .  
             str_pad($emp['position_name'], 15) .    
             str_pad($emp['designation'], 15) .
             str_pad($emp['status_name'], 15) .      
             str_pad($emp['hours_worked'], 8) .
             str_pad($emp['rate_per_hour'], 8) .
             str_pad($emp['ot_hours'], 8) .
             str_pad(number_format($emp['gross_pay'], 2), 12) . "\n"; // format to 2 decimal places
    }
}


// edit function
function editEmployee($employeeDAO, $departments, $positions, $statuses) {
    echo "Enter Employee ID to edit: "; // input the ID to edit
    $emp_id = trim(fgets(STDIN));

    $employee = (array) $employeeDAO->getEmployeeById($emp_id);  // converts the result from getEmployeeById to an associative array (similar to dictionary in python)
    if (!$employee) {
        echo "Employee not found.\n";
        return;
    }

    echo "Enter New Employee Name ({$employee['emp_name']}): ";
    $emp_name = trim(fgets(STDIN)) ?: $employee['emp_name'];

    echo "Enter New Date Hired ({$employee['date_hired']}): ";
    $date_hired = trim(fgets(STDIN)) ?: $employee['date_hired'];

    echo "Enter New Designation ({$employee['designation']}): ";
    $designation = trim(fgets(STDIN)) ?: $employee['designation'];

    // gets ids for dept, position, and status
    $dept_id = selectOption("Select New Department:", $departments);
    $position_id = selectOption("Select New Position:", $positions);
    $status_id = selectOption("Select New Employment Status:", $statuses);
    

    echo "Enter New Hours Worked ({$employee['hours_worked']}): ";
    $input = trim(fgets(STDIN));
    $hours_worked = ($input !== '') ? (float)$input : $employee['hours_worked'];

    echo "Enter New Rate per Hour ({$employee['rate_per_hour']}): ";
    $input = trim(fgets(STDIN));
    $rate_per_hour = ($input !== '') ? (float)$input : $employee['rate_per_hour'];

    echo "Enter New Overtime Hours ({$employee['ot_hours']}): ";
    $input = trim(fgets(STDIN));
    $ot_hours = ($input !== '') ? (float)$input : $employee['ot_hours'];

    // employee object
    $employee = new Employee(
        $emp_id, $emp_name, $date_hired, $dept_id,
        $position_id, $designation, $status_id,
        $hours_worked, $rate_per_hour, $ot_hours
    );
    // gross pay computation
    $gross_pay = $employee->computeGrossPay();

    // updating database
    $updated = $employeeDAO->updateEmployee($emp_id, [
        'emp_name' => $emp_name,
        'date_hired' => $date_hired,
        'dept_id' => $dept_id,
        'position_id' => $position_id,
        'designation' => $designation,
        'employment_status' => $status_id, // Change this from 'status_id' to match the function's expected key
        'hours_worked' => $hours_worked,
        'rate_per_hour' => $rate_per_hour,
        'ot_hours' => $ot_hours,
        'gross_pay' => $gross_pay
    ]);
    

    if ($updated) {
        echo "Employee record updated successfully!\n";
    } else {
        echo "Error updating employee record.\n";
    }
}


// delete function
function deleteEmployee($employeeDAO) {
    echo "Enter Employee ID to delete: ";
    $emp_id = trim(fgets(STDIN));

    // get employees
    $employees = $employeeDAO->listEmployees();

    // built in function to filter employees to find the one with the matching emp_id
    $employee = array_filter($employees, fn($e) => $e['emp_id'] == $emp_id);

    if (empty($employee)) {
        echo "Employee not found.\n";
        return;
    }

    // built in reset function to get first elem ent of the filtered employee
    $employee = reset($employee);
    echo "Are you sure you want to delete {$employee['emp_name']}? (yes/no): ";
    $confirm = trim(fgets(STDIN));

    // converts string to lower case
    if (strtolower($confirm) === 'yes') {
        // uses delete function from DAO
        $deleted = $employeeDAO->deleteEmployee($emp_id);
    } else {
        echo "Deletion canceled.\n";
    }
}



?>
