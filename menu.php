<?php

function showMenu() {
    echo "\n=== PAYROLL SYSTEM ===\n";
    echo "1. Add Employee\n";
    echo "2. View Employees\n";
    echo "3. Edit Employee\n";
    echo "4. Delete Employee\n";
    echo "5. Exit\n";
    echo "Select an option: ";
    return trim(fgets(STDIN));
}
?>
