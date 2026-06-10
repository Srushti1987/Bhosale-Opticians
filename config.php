<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sunray_db');

// Razorpay configuration
// Get your keys from: https://dashboard.razorpay.com/app/keys
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID'); // Replace with your Test Key ID
define('RAZORPAY_KEY_SECRET', 'YOUR_KEY_SECRET'); // Replace with your Test Key Secret

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
