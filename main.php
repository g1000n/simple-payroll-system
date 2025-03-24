<?php

require_once 'EmployeeDAO.php';

$employeeDAO = new EmployeeDAO();

// values/arrays that can be used for operations such as editing
$departments = [
    1 => "HUMAN RESOURCES",
    2 => "INFORMATION TECHNOLOGY",
    3 => "FINANCE",
    4 => "MARKETING",
    5 => "RESEARCH AND DEVELOPMENT"
];

$positions = [
    1 => "DIRECTOR",
    2 => "ASSISTANT",
    3 => "MANAGEMENT",
    4 => "ANALYST",
    5 => "STAFF"
];

$statuses = [
    1 => "PROBATION",
    2 => "PERMANENT",
    3 => "CONTRACTUAL"
];

// start of user interaction
while (true) {
    echo "\n=== PAYROLL SYSTEM ===\n";
    echo "1. Add Employee\n";
    echo "2. View Employees\n";
    echo "3. Edit Employee\n";
    echo "4. Delete Employee\n";
    echo "5. Exit\n";

    echo "Select an option: ";
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        // adding employee
        case "1":
            echo "Enter Employee ID: ";
            $emp_id = trim(fgets(STDIN));
            echo "Enter Employee Name: ";
            $emp_name = trim(fgets(STDIN));
            echo "Enter Date Hired (YYYY-MM-DD): ";
            $date_hired = trim(fgets(STDIN));

            // select from dept array
            echo "Select Department:\n";
            foreach ($departments as $key => $value) {
                echo "$key. $value\n";
            }
            echo "Enter Choice: ";
            $dept_choice = trim(fgets(STDIN));
            $dept = $departments[$dept_choice] ?? "N/A";

            // select from position array
            echo "Select Position:\n";
            foreach ($positions as $key => $value) {
                echo "$key. $value\n";
            }
            echo "Enter Choice: ";
            $position_choice = trim(fgets(STDIN));
            $position = $positions[$position_choice] ?? "N/A";

            echo "Enter Designation: ";
            $designation = trim(fgets(STDIN));

            // select from employment status array
            echo "Select Employment Status:\n";
            foreach ($statuses as $key => $value) {
                echo "$key. $value\n";
            }
            echo "Enter Choice: ";
            $status_choice = trim(fgets(STDIN));
            $employment_status = $statuses[$status_choice] ?? "N/A";

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

            break;

        // view employees
        case "2":
            $employees = $employeeDAO->listEmployees();
            if (empty($employees)) {
                echo "\nNo employees found.\n";
                break;
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
            break;

        // edit employe
        case "3":
            echo "Enter Employee ID to edit: ";
            $emp_id = trim(fgets(STDIN));
            $employees = $employeeDAO->listEmployees();
        
            // find employee by ID
            $employee = array_filter($employees, fn($e) => $e['emp_id'] == $emp_id);
            if (empty($employee)) {
                echo "Employee not found.\n";
                break;
            }
            $employee = reset($employee); // get the first matching employee
        
            echo "Enter New Employee Name ({$employee['emp_name']}): ";
            $emp_name = trim(fgets(STDIN)) ?: $employee['emp_name'];

            echo "Enter New Date Hired ({$employee['date_hired']}): ";
            $date_hired = trim(fgets(STDIN)) ?: $employee['date_hired'];

            echo "Select New Department ({$employee['dept']}):\n";
            foreach ($departments as $key => $value) {
                echo "$key. $value\n";
            }
            $dept_choice = trim(fgets(STDIN));
            $dept = $departments[$dept_choice] ?? $employee['dept'];

            echo "Select New Position ({$employee['position']}):\n";
            foreach ($positions as $key => $value) {
                echo "$key. $value\n";
            }
            $position_choice = trim(fgets(STDIN));
            $position = $positions[$position_choice] ?? $employee['position'];

            echo "Enter New Designation ({$employee['designation']}): ";
            $designation = trim(fgets(STDIN)) ?: $employee['designation'];

            echo "Select New Employment Status ({$employee['employment_status']}):\n";
            foreach ($statuses as $key => $value) {
                echo "$key. $value\n";
            }
            $status_choice = trim(fgets(STDIN));
            $employment_status = $statuses[$status_choice] ?? $employee['employment_status'];

            echo "Enter New Hours Worked ({$employee['hours_worked']}): ";
            $hours_worked = trim(fgets(STDIN)) ?: $employee['hours_worked'];

            echo "Enter New Rate per Hour ({$employee['rate_per_hour']}): ";
            $rate_per_hour = trim(fgets(STDIN)) ?: $employee['rate_per_hour'];

            echo "Enter New OT Hours ({$employee['ot_hours']}): ";
            $ot_hours = trim(fgets(STDIN)) ?: $employee['ot_hours'];

            $gross_pay = ($hours_worked * $rate_per_hour) + ($ot_hours * $rate_per_hour * 1.5);

            $employeeDAO->editEmployee($emp_id, compact(
                'emp_name', 'date_hired', 'dept', 'position',
                'designation', 'employment_status', 'hours_worked',
                'rate_per_hour', 'ot_hours', 'gross_pay'
            ));
            break;

        case "4":
            echo "Enter Employee ID to delete: ";
            $emp_id = trim(fgets(STDIN));
            
            // Confirm before deleting
            echo "Are you sure you want to delete Employee ID $emp_id? (yes/no): ";
            $confirm = trim(fgets(STDIN));
        
            if (strtolower($confirm) === "yes") {
                $employeeDAO->deleteEmployee($emp_id);
            } else {
                echo "Deletion canceled.\n";
            }
            break;

        case "5":
            echo "Exiting program...\n";
            exit;

        default:
            echo "Invalid option. Try again.\n";
    }
}
