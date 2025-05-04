<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Aerospace Airways</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            background-color: #f9f9f9;
        }
        header.admin-header {
            background-color: #e91d64;
            padding: 6px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header.admin-header nav a {
            color: white;
            margin-right: 20px;
            font-weight: 600;
            text-decoration: none;
        }
        header.admin-header nav a:hover {
            text-decoration: underline;
        }
        .admin-logout-btn {
            background-color: #c41854;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        .admin-logout-btn:hover {
            background-color: #a31542;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <nav>
                <a href="admin-dashboard.php">Dashboard</a>
                <a href="admin-bookings.php">Bookings</a>
                <a href="admin-contact-messages.php">Contact Messages</a>
                <a href="admin-flights.php">Flights</a>
                <a href="admin-add-flight.php">Add Flights</a>
                <a href="admin-delete-flight.php">Delete Flights</a>
            </nav>
            <a href="admin-logout.php" class="admin-logout-btn">Logout</a>
        </div>
    </header>
</body>
</html>
