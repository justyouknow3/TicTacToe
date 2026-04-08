<<<<<<< HEAD
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
=======
<<<<<<< HEAD
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
=======
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
>>>>>>> e7f6847 (first update)
>>>>>>> 4062a24 (first update)
