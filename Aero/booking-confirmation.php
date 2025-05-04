<?php
session_start();
$page_title = "Booking Confirmation";
include 'includes/db_connect.php';

// Enforce user login before booking confirmation
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$pnr = $_GET['pnr'] ?? '';

if (!$pnr) {
    die("Invalid booking reference. Please provide a valid PNR.");
}

// Fetch booking details
$booking_sql = "SELECT b.*, f.flight_number, f.origin, f.destination, f.departure_time, f.arrival_time, f.price
                FROM bookings b
                JOIN flights f ON b.flight_id = f.id
                WHERE b.pnr = ?";
$stmt = $conn->prepare($booking_sql);
$stmt->bind_param("s", $pnr);
$stmt->execute();
$booking_result = $stmt->get_result();

if ($booking_result->num_rows == 0) {
    die("Booking not found for the provided PNR.");
}

$booking = $booking_result->fetch_assoc();

// Fetch passengers
$passengers_sql = "SELECT * FROM passengers WHERE booking_id = ?";
$passengers_stmt = $conn->prepare($passengers_sql);
$passengers_stmt->bind_param("i", $booking['id']);
$passengers_stmt->execute();
$passengers_result = $passengers_stmt->get_result();

include 'includes/header.php';
?>

<main>
    <section class="page-header" style="margin-bottom: 20px;">
        <div class="container">
            <h1>Booking Confirmation</h1>
        </div>
    </section>

    <section class="booking-confirmation-section">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">Your Booking Reference (PNR): <?php echo htmlspecialchars($booking['pnr']); ?></h2>
            <div class="flight-details-card" style="margin-bottom: 30px; padding: 15px; background: #fff; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 15px;">Flight Details</h3>
                <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
                <p><strong>From:</strong> <?php echo htmlspecialchars($booking['origin']); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                <p><strong>Total Amount Paid:</strong> <?php echo htmlspecialchars($booking['total_amount']); ?> INR</p>
            </div>

            <div class="passenger-details-card" style="background: #fff; padding: 15px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 15px;">Passenger Details</h3>
                <ul style="list-style-type: none; padding-left: 0;">
                    <?php while ($passenger = $passengers_result->fetch_assoc()): ?>
                        <li style="margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <strong><?php echo htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']); ?></strong><br>
                            Gender: <?php echo htmlspecialchars($passenger['gender']); ?><br>
                            Age: <?php echo htmlspecialchars($passenger['age']); ?><br>
                            Checked In: <?php echo $passenger['checked_in'] ? 'Yes' : 'No'; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <div class="ticket-download" style="text-align: center; margin-top: 30px;">
                <button onclick="window.print();" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">Download / Print Ticket</button>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
