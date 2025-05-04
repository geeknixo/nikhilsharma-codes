<?php
session_start();
$page_title = "Booking Details";
include 'includes/db_connect.php';

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    die("Booking ID is required to edit booking.");
}

$error_message = "";
$success_message = "";

// Fetch booking and flight details
$sql = "SELECT b.*, f.flight_number, f.origin, f.destination, f.departure_time, f.arrival_time, f.price
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Booking not found.");
} else {
    $booking = $result->fetch_assoc();
}

// Fetch passengers
$passengers_sql = "SELECT * FROM passengers WHERE booking_id = ?";
$passengers_stmt = $conn->prepare($passengers_sql);
$passengers_stmt->bind_param("i", $booking_id);
$passengers_stmt->execute();
$passengers_result = $passengers_stmt->get_result();
$passengers = [];
while ($row = $passengers_result->fetch_assoc()) {
    $passengers[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $update_error = false;
        foreach ($passengers as $passenger) {
            $pid = $passenger['id'];
            $first_name = trim($_POST["first_name_$pid"] ?? '');
            $last_name = trim($_POST["last_name_$pid"] ?? '');
            $gender = $_POST["gender_$pid"] ?? '';
            $age = intval($_POST["age_$pid"] ?? 0);

            if (!$first_name || !$last_name || !$gender || $age < 1) {
                $error_message = "Please fill in all required fields for all passengers.";
                $update_error = true;
                break;
            }

            $update_sql = "UPDATE passengers SET first_name=?, last_name=?, gender=?, age=? WHERE id=?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssii", $first_name, $last_name, $gender, $age, $pid);
            if (!$update_stmt->execute()) {
                $error_message = "Failed to update passenger details.";
                $update_error = true;
                break;
            }
        }
        if (!$update_error) {
            header("Location: booking-details.php?booking_id=" . urlencode($booking_id) . "&updated=1");
            exit();
        }
    } elseif (isset($_POST['cancel'])) {
        $delete_passengers_sql = "DELETE FROM passengers WHERE booking_id = ?";
        $delete_passengers_stmt = $conn->prepare($delete_passengers_sql);
        $delete_passengers_stmt->bind_param("i", $booking_id);
        $delete_passengers_stmt->execute();

        $delete_booking_sql = "DELETE FROM bookings WHERE id = ?";
        $delete_booking_stmt = $conn->prepare($delete_booking_sql);
        $delete_booking_stmt->bind_param("i", $booking_id);
        $delete_booking_stmt->execute();

        header("Location: manage-booking.php?cancelled=1");
        exit();
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header" style="margin-bottom: 20px;">
        <div class="container">
            <h1>Booking Details</h1>
        </div>
    </section>

    <section class="booking-details-section">
        <div class="container" style="max-width: 900px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <?php if ($error_message): ?>
                <div class="alert alert-error" style="color: red; margin-bottom: 20px;"><?php echo $error_message; ?></div>
            <?php elseif (isset($_GET['updated'])): ?>
                <div class="alert alert-success" style="color: green; margin-bottom: 20px;">Passenger details updated successfully.</div>
                <div class="flight-details-card" style="margin-bottom: 30px; padding: 15px; background: #fff; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                    <h2 style="margin-bottom: 15px;">Flight Details</h2>
                    <p><strong>PNR:</strong> <?php echo htmlspecialchars($booking['pnr']); ?></p>
                    <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($booking['origin']); ?></p>
                    <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                    <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                    <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                    <p><strong>Total Amount Paid:</strong> <?php echo htmlspecialchars($booking['total_amount']); ?> INR</p>
                </div>
                <div class="passenger-details-card" style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                    <h2 style="margin-bottom: 20px;">Passenger Details</h2>
                    <?php foreach ($passengers as $passenger): ?>
                        <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']); ?></p>
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($passenger['gender']); ?></p>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($passenger['age']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="flight-details-card" style="margin-bottom: 30px; padding: 15px; background: #fff; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                    <h2 style="margin-bottom: 15px;">Flight Details</h2>
                    <p><strong>PNR:</strong> <?php echo htmlspecialchars($booking['pnr']); ?></p>
                    <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($booking['origin']); ?></p>
                    <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                    <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                    <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                    <p><strong>Total Amount Paid:</strong> <?php echo htmlspecialchars($booking['total_amount']); ?> INR</p>
                </div>

                <form action="" method="POST" style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                    <h2 style="margin-bottom: 20px;">Passenger Details</h2>
                    <?php foreach ($passengers as $passenger): ?>
                        <fieldset style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                            <legend style="font-weight: bold; padding: 0 10px;"><?php echo htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']); ?></legend>
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label for="first_name_<?php echo $passenger['id']; ?>" style="display: block; margin-bottom: 5px;">First Name</label>
                                <input type="text" id="first_name_<?php echo $passenger['id']; ?>" name="first_name_<?php echo $passenger['id']; ?>" value="<?php echo htmlspecialchars($passenger['first_name']); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                            </div>
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label for="last_name_<?php echo $passenger['id']; ?>" style="display: block; margin-bottom: 5px;">Last Name</label>
                                <input type="text" id="last_name_<?php echo $passenger['id']; ?>" name="last_name_<?php echo $passenger['id']; ?>" value="<?php echo htmlspecialchars($passenger['last_name']); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                            </div>
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label for="gender_<?php echo $passenger['id']; ?>" style="display: block; margin-bottom: 5px;">Gender</label>
                                <select id="gender_<?php echo $passenger['id']; ?>" name="gender_<?php echo $passenger['id']; ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php if ($passenger['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if ($passenger['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if ($passenger['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label for="age_<?php echo $passenger['id']; ?>" style="display: block; margin-bottom: 5px;">Age</label>
                                <input type="number" id="age_<?php echo $passenger['id']; ?>" name="age_<?php echo $passenger['id']; ?>" min="1" value="<?php echo htmlspecialchars($passenger['age']); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                            </div>
                        </fieldset>
                    <?php endforeach; ?>
                    <div style="text-align: center;">
                        <button type="submit" name="update" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px; margin-right: 10px;">Update Details</button>
                        <button type="submit" name="cancel" class="btn btn-danger" style="padding: 10px 30px; font-size: 16px;" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">Cancel Booking</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
