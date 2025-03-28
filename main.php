<?php
// import files
require_once 'EmployeeDAO.php';
require_once 'menu.php';
require_once 'choice.php';
require_once 'functions.php';

$employeeDAO = new EmployeeDAO();

while (true) {
    $choice = showMenu();
    // basically like switch statement
    match ($choice) {
        "1" => addEmployee($employeeDAO, $departments, $positions, $statuses),
        "2" => viewEmployees($employeeDAO),
        "3" => editEmployee($employeeDAO, $departments, $positions, $statuses),
        "4" => deleteEmployee($employeeDAO),
        "5" => exit("Exiting program...\n"),
        default => print("Invalid option. Try again.\n")
    };
}
?>
