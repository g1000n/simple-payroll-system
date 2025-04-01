<?php


// validates if the user is logged in and is an admin
if (!isset($_SESSION['emp_id']) || $_SESSION['role'] !== 'admin') {
    echo "You are not logged in or do not have permission to access this page.\n";
    exit;
}

// imports EmployeeDAO.php, menu.php, and functions.php
require_once "EmployeeDAO.php";
require_once "menu.php";
require_once "functions.php";

$employeeDAO = new EmployeeDAO();

while (true) {
    // menu
    $choice = showAdminMenu();

    // choices
    match ($choice) {
        "1" => addEmployee($employeeDAO),
        "2" => viewEmployees($employeeDAO),
        "3" => editEmployee($employeeDAO),
        "4" => deleteEmployee($employeeDAO),
        "5" => addPayroll($employeeDAO),
        "6" => viewPayrollHistory($employeeDAO),
        "7" => exit("Logging out...\n"),
        default => print("Invalid option. Try again.\n")
    };
}
?>