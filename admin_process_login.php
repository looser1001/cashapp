<?php
session_start();

// Define admin username and password (VERY INSECURE - DO NOT USE IN PRODUCTION)
$admin_username = "admin";
$admin_password = "12345";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: admin.html?error=1");
        exit();
    }
} else {
    header("Location: admin.html");
    exit();
}
?>