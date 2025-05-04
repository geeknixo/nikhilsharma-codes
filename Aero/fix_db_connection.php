<?php
// This script attempts to fix the MySQL user permission issue by creating a new user with proper privileges.
// WARNING: Run this script once and then delete it for security reasons.

$servername = "localhost";
$admin_username = "root"; // Change if needed
$admin_password = "";     // Change if needed

// New user credentials to be created
$new_username = "admin_user";
$new_password = "admin_password"; // Change to a strong password

// Connect as root or admin user
$conn = new mysqli($servername, $admin_username, $admin_password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new user and grant privileges
$sql_create_user = "CREATE USER IF NOT EXISTS '$new_username'@'localhost' IDENTIFIED BY '$new_password';";
$sql_grant_privileges = "GRANT ALL PRIVILEGES ON *.* TO '$new_username'@'localhost' WITH GRANT OPTION;";
$sql_flush = "FLUSH PRIVILEGES;";

if ($conn->query($sql_create_user) === TRUE &&
    $conn->query($sql_grant_privileges) === TRUE &&
    $conn->query($sql_flush) === TRUE) {
    echo "New user '$new_username' created and granted privileges successfully.\\n";
    echo "Update your includes/db_connect.php to use these credentials.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
