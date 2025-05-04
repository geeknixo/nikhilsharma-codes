<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aerspace Airlines - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<?php
    $current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<body class="page-<?php echo $current_page; ?>">
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo1.png" alt="Aero Logo">
                </a>
            </div>
            <nav>
                <ul class="main-nav">
                    <li><a href="index.php">Book</a></li>
                    <li><a href="manage-booking.php">Manage</a></li>
                    <li><a href="user-bookings.php">Book history</a></li>
                    <li><a href="contact-us.php">Contact Us</a></li>
                    <li><a href="about.php">About</a></li>

                </ul>
            </nav>
            <div class="user-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-outline">My Account</a>
                    <a href="logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline header-btn">Login</a>
                    <a href="register.php" class="btn btn-primary header-btn">Sign Up</a>
                    <a href="admin-login.php" class="btn btn-outline header-btn">Admin Login</a>
                <?php endif; ?>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    <div class="mobile-menu">
        <ul>
        <li><a href="index.php">Book</a></li>
                    <li><a href="manage-booking.php">Manage</a></li>
                    <li><a href="user-bookings.php">Book history</a></li>
                    <li><a href="contact-us.php">Contact Us</a></li>
                    <li><a href="about.php">About</a></li>

<li><a href="about.php">About</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">My Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>