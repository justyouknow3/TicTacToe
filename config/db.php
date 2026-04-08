<?php
// Database connection using mysqli with simple error handling.
$host = "localhost";
$username = "root";
$password = "";
$database = "bsit_feedback_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
