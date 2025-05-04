<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$delete_success = "";
$delete_error = "";

// Handle delete flight
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_flight_id'])) {
    $delete_id = intval($_POST['delete_flight_id']);
    $sql = "DELETE FROM flights WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $delete_success = "Flight deleted successfully.";
        } else {
            $delete_error = "Error deleting flight: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all flights
$flights = [];
$sql = "SELECT * FROM flights ORDER BY departure_time ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}

include 'admin-header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Delete Flights</h1>
        </div>
    </section>

    <section class="container">
        <?php if ($delete_success): ?>
            <div class="alert alert-success"><?php echo $delete_success; ?></div>
        <?php endif; ?>
        <?php if ($delete_error): ?>
            <div class="alert alert-error"><?php echo $delete_error; ?></div>
        <?php endif; ?>

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
                        <th>Action</th>
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
                            <td>
                                <form method="POST" action="admin-delete-flight.php" onsubmit="return confirm('Are you sure you want to delete this flight?');">
                                    <input type="hidden" name="delete_flight_id" value="<?php echo $flight['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
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
