<?php
define('DB_HOST', 'localhost:3307'); // Use 3306 if that's your default MySQL port
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ebooks_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Confirm connection (for debugging)
// echo "Connected successfully";

// Set charset
$conn->set_charset("utf8mb4");
?>
