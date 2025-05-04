<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$contact_messages = [];
$contact_sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
$contact_result = $conn->query($contact_sql);
if ($contact_result && $contact_result->num_rows > 0) {
    while ($row = $contact_result->fetch_assoc()) {
        $contact_messages[] = $row;
    }
}

$page_title = "Contact Messages";
include 'admin-header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Contact Messages</h1>
        </div>
    </section>

    <section class="container">
        <?php if (!empty($contact_messages)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contact_messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                            <td><?php echo htmlspecialchars($message['submitted_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No contact messages found.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
