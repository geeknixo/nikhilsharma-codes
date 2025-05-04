<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$bookings = [];
$bookings_sql = "SELECT b.id, b.pnr, b.total_amount, b.booking_date, f.flight_number, COUNT(p.id) AS passenger_count
                 FROM bookings b
                 JOIN flights f ON b.flight_id = f.id
                 LEFT JOIN passengers p ON p.booking_id = b.id
                 GROUP BY b.id
                 ORDER BY b.booking_date DESC";
$bookings_result = $conn->query($bookings_sql);
if ($bookings_result && $bookings_result->num_rows > 0) {
    while ($row = $bookings_result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$page_title = "Bookings";
include 'admin-header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Bookings</h1>
        </div>
    </section>

    <section class="container">
        <?php if (!empty($bookings)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>PNR</th>
                        <th>Flight Number</th>
                        <th>Passenger Count</th>
                        <th>Total Amount (INR)</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['pnr']); ?></td>
                            <td><?php echo htmlspecialchars($booking['flight_number']); ?></td>
                            <td><?php echo htmlspecialchars($booking['passenger_count']); ?></td>
                            <td><?php echo htmlspecialchars($booking['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
