<?php

// employee_console.php
require_once "EmployeeDAO.php";
require_once "menu.php";
require_once "functions.php";


// ensures emp_id is set in session
if (!isset($_SESSION['emp_id'])) {
    exit("Error: Employee ID not found. Please log in again.\n");
}

$emp_id = $_SESSION['emp_id']; // gets employee ID from the session
$employeeDAO = new EmployeeDAO();

while (true) {
    $choice = showEmployeeMenu();

    match ($choice) {
        "1" => viewEmployees($employeeDAO, $emp_id), // views employee details
        "2" => viewOwnPayroll($employeeDAO, $emp_id),   // views payroll for logged-in employee
        "3" => exit("Logging out...\n"),             // logout option
        default => print("Invalid option. Try again.\n")
    };
}

?>