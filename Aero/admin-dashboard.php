<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$add_error = "";
$add_success = "";
$delete_success = "";
$delete_error = "";

// Handle add flight form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_flight'])) {
    $flight_number = trim($_POST['flight_number'] ?? '');
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $departure_time = trim($_POST['departure_time'] ?? '');
    $arrival_time = trim($_POST['arrival_time'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $available_seats = trim($_POST['available_seats'] ?? '');
    $status = trim($_POST['status'] ?? 'On Time');

    if ($flight_number && $origin && $destination && $departure_time && $arrival_time && $price && $available_seats) {
        $sql = "INSERT INTO flights (flight_number, origin, destination, departure_time, arrival_time, price, available_seats, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssdiss", $flight_number, $origin, $destination, $departure_time, $arrival_time, $price, $available_seats, $status);
            if ($stmt->execute()) {
                $add_success = "Flight added successfully.";
            } else {
                $add_error = "Error adding flight: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $add_error = "Please fill in all required fields.";
    }
}

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

// Fetch all bookings with flight info
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

// Fetch all contact messages
$contact_messages = [];
$contact_sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
$contact_result = $conn->query($contact_sql);
if ($contact_result && $contact_result->num_rows > 0) {
    while ($row = $contact_result->fetch_assoc()) {
        $contact_messages[] = $row;
    }
}
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Admin Dashboard</h1>
        </div>
    </section>

    <section class="container">
        <h2>Bookings</h2>
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

        <h2>Contact Messages</h2>
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

        <h2>Existing Flights</h2>
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
