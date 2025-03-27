<?php

function addEmployee($employeeDAO, $departments, $positions, $statuses) {
    echo "Enter Employee ID: ";
    $emp_id = trim(fgets(STDIN));
    echo "Enter Employee Name: ";
    $emp_name = trim(fgets(STDIN));
    echo "Enter Date Hired (YYYY-MM-DD): ";
    $date_hired = trim(fgets(STDIN));

    $dept = selectOption("Select Department:", $departments);
    $position = selectOption("Select Position:", $positions);
    echo "Enter Designation: ";
    $designation = trim(fgets(STDIN));
    $employment_status = selectOption("Select Employment Status:", $statuses);

    echo "Enter Hours Worked: ";
    $hours_worked = (float) trim(fgets(STDIN));
    echo "Enter Rate per Hour: ";
    $rate_per_hour = (float) trim(fgets(STDIN));
    echo "Enter Overtime Hours: ";
    $ot_hours = (float) trim(fgets(STDIN));

    $employee = new Employee(
        $emp_id, $emp_name, $date_hired, $dept,
        $position, $designation, $employment_status,
        $hours_worked, $rate_per_hour, $ot_hours
    );

    $employeeDAO->addEmployee($employee);
    echo "Employee added successfully!\n";
}

function viewEmployees($employeeDAO) {
    $employees = $employeeDAO->listEmployees();
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
             str_pad($emp['dept'], 25) .
             str_pad($emp['position'], 15) .
             str_pad($emp['designation'], 15) .
             str_pad($emp['employment_status'], 15) .
             str_pad($emp['hours_worked'], 8) .
             str_pad($emp['rate_per_hour'], 8) .
             str_pad($emp['ot_hours'], 8) .
             str_pad($emp['gross_pay'], 12) . "\n";
    }
}

function editEmployee($employeeDAO, $departments, $positions, $statuses) {
    echo "Enter Employee ID to edit: ";
    $emp_id = trim(fgets(STDIN));
    $employees = $employeeDAO->listEmployees();
    $employee = array_filter($employees, fn($e) => $e['emp_id'] == $emp_id);
    if (empty($employee)) {
        echo "Employee not found.\n";
        return;
    }
    $employee = reset($employee);

    echo "Enter New Employee Name ({$employee['emp_name']}): ";
    $emp_name = trim(fgets(STDIN)) ?: $employee['emp_name'];
    echo "Enter New Date Hired ({$employee['date_hired']}): ";
    $date_hired = trim(fgets(STDIN)) ?: $employee['date_hired'];
    echo "Enter New Designation ({$employee['designation']}): ";
    $designation = trim(fgets(STDIN)) ?: $employee['designation'];

    echo "Select New Department ({$employee['dept']}):\n";
    foreach ($departments as $key => $value) {
        echo "$key. $value\n";
    }
    echo "Enter Choice: ";
    $dept_choice = trim(fgets(STDIN));
    $dept = $departments[$dept_choice] ?? $employee['dept'];

    echo "Select New Position ({$employee['position']}):\n";
    foreach ($positions as $key => $value) {
        echo "$key. $value\n";
    }
    echo "Enter Choice: ";
    $position_choice = trim(fgets(STDIN));
    $position = $positions[$position_choice] ?? $employee['position'];

    echo "Select New Employment Status ({$employee['employment_status']}):\n";
    foreach ($statuses as $key => $value) {
        echo "$key. $value\n";
    }
    echo "Enter Choice: ";
    $status_choice = trim(fgets(STDIN));
    $employment_status = $statuses[$status_choice] ?? $employee['employment_status'];

    echo "Enter New Hours Worked ({$employee['hours_worked']}): ";
    $hours_worked = trim(fgets(STDIN));
    $hours_worked = $hours_worked !== "" ? (float) $hours_worked : (float) $employee['hours_worked'];

    echo "Enter New Rate per Hour ({$employee['rate_per_hour']}): ";
    $rate_per_hour = trim(fgets(STDIN));
    $rate_per_hour = $rate_per_hour !== "" ? (float) $rate_per_hour : (float) $employee['rate_per_hour'];

    echo "Enter New Overtime Hours ({$employee['ot_hours']}): ";
    $ot_hours = trim(fgets(STDIN));
    $ot_hours = $ot_hours !== "" ? (float) $ot_hours : (float) $employee['ot_hours'];

    // Create Employee Object and Compute Gross Pay
    $emp = new Employee($emp_id, $emp_name, $date_hired, $dept, $position, $designation, $employment_status, $hours_worked, $rate_per_hour, $ot_hours);
    $gross_pay = $emp->computeGrossPay(); // Use class method to compute gross pay

    // Update Employee Record
    $employeeDAO->editEmployee($emp_id, compact('emp_name', 'date_hired', 'dept', 'position', 'designation', 'employment_status', 'hours_worked', 'rate_per_hour', 'ot_hours', 'gross_pay'));

}

function deleteEmployee($employeeDAO) {
    echo "Enter Employee ID to delete: ";
    $emp_id = trim(fgets(STDIN));

    $employees = $employeeDAO->listEmployees();
    $employee = array_filter($employees, fn($e) => $e['emp_id'] == $emp_id);

    if (empty($employee)) {
        echo "Employee not found.\n";
        return;
    }

    $employee = reset($employee);
    echo "Are you sure you want to delete {$employee['emp_name']}? (yes/no): ";
    $confirm = trim(fgets(STDIN));

    if (strtolower($confirm) === 'yes') {
        $employeeDAO->deleteEmployee($emp_id);
        echo "Employee record deleted successfully!\n";
    } else {
        echo "Deletion canceled.\n";
    }
}

function selectOption($message, $options) {
    echo "\n$message\n";
    foreach ($options as $key => $value) {
        echo "$key. $value\n";
    }
    echo "Enter Choice: ";
    $choice = trim(fgets(STDIN));
    return $options[$choice] ?? "N/A";
}


?>
