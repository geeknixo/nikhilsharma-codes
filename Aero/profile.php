<?php
session_start();
$page_title = "My Account";
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from database
include 'includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // User not found, redirect to login
    header("Location: login.php");
    exit();
}
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>My Account</h1>
        </div>
    </section>

    <section class="account-details">
        <div class="container">
            <h2>Account Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
