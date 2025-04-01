<?php
// main.php
require_once "auth.php";

$auth = new Auth();

// login
echo "=== Payroll System Login ===\n";
echo "Username: ";
$username = trim(fgets(STDIN));
echo "Password: ";
$password = trim(fgets(STDIN));

$userData = $auth->login($username, $password);


if ($userData) {
    echo "Login successful!\n";

    // Store user data in session
    $_SESSION['emp_id'] = $userData['emp_id'];  // Store the employee ID
    $_SESSION['role'] = $userData['role'];      // Store the role (admin/employee)

    // Redirect based on the role
    if ($_SESSION['role'] === 'admin') {
        echo "Redirecting to admin console...\n";
        require_once "admin_console.php";  // Admin console/dashboard
    } else {
        echo "Redirecting to employee console...\n";
        require_once "employee_console.php";  // Employee console/dashboard
    }
} else {
    echo "Invalid credentials!\n";
}

?>
