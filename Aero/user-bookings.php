<?php
session_start();
$page_title = "My Bookings";
include 'includes/db_connect.php';

// Enforce user login
if (!isset($_SESSION['user_id'])) {
    $redirect_url = 'user-bookings.php';
    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit();
}

$user_id = $_SESSION['user_id'];

// Get logged-in user's email
$user_email = '';
$user_sql = "SELECT email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_row = $user_result->fetch_assoc()) {
    $user_email = $user_row['email'];
}

// Fetch bookings for the logged-in user by matching passenger email
$bookings = [];
$sql = "SELECT DISTINCT b.id, b.pnr, b.total_amount, b.booking_date, b.status, f.flight_number, f.origin, f.destination, f.departure_time, f.arrival_time
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        JOIN passengers p ON b.id = p.booking_id
        WHERE p.email = ?
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

// Fetch passengers for each booking
$passengers_by_booking = [];
if (!empty($bookings)) {
    $booking_ids = array_column($bookings, 'id');
    $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
    $types = str_repeat('i', count($booking_ids));
    $sql_passengers = "SELECT * FROM passengers WHERE booking_id IN ($placeholders)";
    $stmt_passengers = $conn->prepare($sql_passengers);
    $stmt_passengers->bind_param($types, ...$booking_ids);
    $stmt_passengers->execute();
    $result_passengers = $stmt_passengers->get_result();
    while ($p = $result_passengers->fetch_assoc()) {
        $passengers_by_booking[$p['booking_id']][] = $p;
    }
}

include 'includes/header.php';
?>

<style>
    .booking-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
        transition: box-shadow 0.3s ease;
    }
    .booking-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }
    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .booking-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: bold;
        color: #fff;
    }
    .status-Confirmed {
        background-color: #28a745;
    }
    .status-Cancelled {
        background-color: #dc3545;
    }
    .status-Pending {
        background-color: #ffc107;
        color: #212529;
    }
    .passenger-list {
        list-style: none;
        padding-left: 0;
    }
    .passenger-list li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 15px;
    }
    h1 {
        text-align: center;
        margin-bottom: 40px;
        font-weight: 700;
        color: #e91d64;
    }
</style>

<main>
    <div class="container">
        <h1>My Bookings</h1>
        <?php if (empty($bookings)): ?>
            <p>You have no bookings yet.</p>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <strong>PNR:</strong> <?php echo htmlspecialchars($booking['pnr']); ?><br>
                            <strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?>
                        </div>
                        <div class="booking-status status-<?php echo htmlspecialchars($booking['status']); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </div>
                    </div>
                    <div>
                        <h3>Flight Details</h3>
                        <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
                        <p><strong>From:</strong> <?php echo htmlspecialchars($booking['origin']); ?></p>
                        <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                        <p><strong>Departure:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                        <p><strong>Arrival:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                        <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($booking['total_amount']); ?> INR</p>
                    </div>
                    <div>
                        <h3>Passengers</h3>
                        <ul class="passenger-list">
                            <?php
                            $passengers = $passengers_by_booking[$booking['id']] ?? [];
                            foreach ($passengers as $p):
                            ?>
                                <li>
                                    <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?> -
                                    Age: <?php echo htmlspecialchars($p['age']); ?>,
                                    Gender: <?php echo htmlspecialchars($p['gender']); ?>,
                                    Email: <?php echo htmlspecialchars($p['email']); ?>,
                                    Phone: <?php echo htmlspecialchars($p['phone']); ?>,
                                    Checked In: <?php echo $p['checked_in'] ? 'Yes' : 'No'; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
