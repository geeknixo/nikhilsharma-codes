<?php
$servername = "localhost";
$username = "admin_user";  // Updated MySQL username
$password = "admin_password";      // Updated MySQL password
$dbname = "indigo_airlines";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>