<?php
// function that displays the admin menu and gets the user's selection
function showAdminMenu() {
    echo "\n=== Admin Menu ===\n";
    echo "1. Add Employee\n";
    echo "2. View Employees\n";
    echo "3. Edit Employee\n";
    echo "4. Delete Employee\n";
    echo "5. Process Payroll\n";
    echo "6. View Payroll History\n";
    echo "7. Logout\n";
    echo "Select an option: ";
    return trim(fgets(STDIN));
}

// function that displays employee menu and also gets the user's selection
function showEmployeeMenu() {
    echo "\n=== Employee Menu ===\n";
    echo "1. View Employee Details\n";
    echo "2. View Payroll History\n";
    echo "3. Logout\n";
    echo "Select an option: ";
    return trim(fgets(STDIN));
}
?>
