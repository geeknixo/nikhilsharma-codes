<?php
session_start();
$page_title = "Manage Booking";
include 'includes/db_connect.php';

$error_message = "";
$success_message = "";
$booking = null;
$passengers = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cancel'])) {
        // Handle cancellation
        $cancel_pnr = $_POST['cancel_pnr'] ?? '';
        if ($cancel_pnr) {
            // Delete booking and passengers
            $booking_id_sql = "SELECT id FROM bookings WHERE pnr = ?";
            $stmt = $conn->prepare($booking_id_sql);
            $stmt->bind_param("s", $cancel_pnr);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $booking_row = $result->fetch_assoc();
                $booking_id = $booking_row['id'];

                $delete_passengers_sql = "DELETE FROM passengers WHERE booking_id = ?";
                $delete_passengers_stmt = $conn->prepare($delete_passengers_sql);
                $delete_passengers_stmt->bind_param("i", $booking_id);
                $delete_passengers_stmt->execute();

                $delete_booking_sql = "DELETE FROM bookings WHERE id = ?";
                $delete_booking_stmt = $conn->prepare($delete_booking_sql);
                $delete_booking_stmt->bind_param("i", $booking_id);
                $delete_booking_stmt->execute();

                $success_message = "Booking cancelled successfully.";
                $booking = null;
                $passengers = [];
            } else {
                $error_message = "Booking not found for cancellation.";
            }
        }
    } else {
        $pnr = trim($_POST['pnr'] ?? '');
        $last_name = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!empty($pnr) && (!empty($last_name) || !empty($email))) {
            // Check if booking exists with PNR and last name or email
            $sql = "SELECT b.*, f.flight_number, f.departure_time, f.origin, f.destination, f.arrival_time
                    FROM bookings b
                    JOIN flights f ON b.flight_id = f.id
                    JOIN passengers p ON b.id = p.booking_id
                    WHERE b.pnr = ? AND (p.last_name = ? OR ? = '')";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $pnr, $last_name, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $booking = $result->fetch_assoc();

                // Fetch passengers
                $passenger_sql = "SELECT * FROM passengers WHERE booking_id = ?";
                $passenger_stmt = $conn->prepare($passenger_sql);
                $passenger_stmt->bind_param("i", $booking['id']);
                $passenger_stmt->execute();
                $passenger_result = $passenger_stmt->get_result();

                while ($row = $passenger_result->fetch_assoc()) {
                    $passengers[] = $row;
                }
            } else {
                $error_message = "No booking found with the provided details.";
            }
        } else {
            $error_message = "Please fill in all required fields.";
        }
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header" style="margin-bottom: 20px; text-align: center;">
        <div class="container">
            <h1 style="color: #e91d64;">Manage Booking</h1>
        </div>
    </section>

    <section class="manage-booking-section">
        <div class="container" style="max-width: 900px; margin: 0 auto;">
            <div class="form-card" style="margin-bottom: 30px;">
                <h2>Retrieve your booking</h2>
                <p>View, modify or cancel your bookings online with ease.</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error" style="color: red; margin-bottom: 20px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" style="color: green; margin-bottom: 20px;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 6px;">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="pnr">PNR / Booking Reference</label>
                        <input type="text" id="pnr" name="pnr" placeholder="Enter 6 character PNR" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Enter passenger's last name" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter email address used for booking" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    </div>

                    <div class="form-submit" style="text-align: center;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">Retrieve Booking</button>
                    </div>
                </form>
            </div>

            <?php if ($booking): ?>
                <div class="booking-details" style="background: #f9f9f9; padding: 20px; border-radius: 6px;">
                    <h2>Booking Details</h2>
                    <p><strong>PNR:</strong> <?php echo htmlspecialchars($booking['pnr']); ?></p>
                    <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($booking['origin']); ?></p>
                    <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                    <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                    <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                    <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($booking['total_amount']); ?> INR</p>

                    <h3>Passengers</h3>
                    <ul>
                        <?php foreach ($passengers as $p): ?>
                            <li><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>, Gender: <?php echo htmlspecialchars($p['gender']); ?>, Age: <?php echo htmlspecialchars($p['age']); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <div style="margin-top: 20px;">
<a href="booking-details.php?booking_id=<?php echo urlencode($booking['id']); ?>" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">Edit Booking</a>
                        <form action="manage-booking.php" method="POST" style="display: inline;">
                            <input type="hidden" name="cancel_pnr" value="<?php echo htmlspecialchars($booking['pnr']); ?>">
                            <button type="submit" name="cancel" class="btn btn-danger" style="padding: 10px 30px; font-size: 16px;" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">Cancel Booking</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
