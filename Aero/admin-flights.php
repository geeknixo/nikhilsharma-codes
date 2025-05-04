<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$flights = [];
$sql = "SELECT * FROM flights ORDER BY departure_time ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}

$page_title = "Flights";
include 'admin-header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Existing Flights</h1>
        </div>
    </section>

    <section class="container">
        <?php if (!empty($flights)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Flight Number</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Price (INR)</th>
                        <th>Available Seats</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                            <td><?php echo htmlspecialchars($flight['origin']); ?></td>
                            <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['departure_time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['arrival_time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['price']); ?></td>
                            <td><?php echo htmlspecialchars($flight['available_seats']); ?></td>
                            <td><?php echo htmlspecialchars($flight['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No flights found.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
