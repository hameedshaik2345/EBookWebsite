<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost:3307');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'ebooks_db');

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
