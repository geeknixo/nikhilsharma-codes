<?php
session_start();
$page_title = "Passenger Details";
include 'includes/db_connect.php';

/* Removed enforced user login before entering passenger details
if (!isset($_SESSION['user_id'])) {
    $redirect_url = $_SERVER['REQUEST_URI'];
    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit();
}
*/

$error_message = "";
$success_message = "";

$from_location = $_GET['from_location'] ?? '';
$to_location = $_GET['to_location'] ?? '';
$num_passengers = isset($_GET['num_passengers']) ? intval($_GET['num_passengers']) : 1;

if (!$from_location || !$to_location || $num_passengers < 1) {
    die("Invalid flight selection. Please go back and select valid flight details.");
}

if (false) { // Disable debug output
    echo "<pre>Debug: from_location = '" . htmlspecialchars($from_location) . "'\n";
    echo "Debug: to_location = '" . htmlspecialchars($to_location) . "'</pre>";
}

$sql = "SELECT * FROM flights WHERE LOWER(TRIM(origin)) = LOWER(TRIM(?)) AND LOWER(TRIM(destination)) = LOWER(TRIM(?)) AND status = 'On Time' ORDER BY departure_time ASC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from_location, $to_location);
$stmt->execute();
$result = $stmt->get_result();

if (false) { // Disable debug output
    echo "<pre>Debug: Number of flights found = " . $result->num_rows . "</pre>";
}

if ($result->num_rows == 0) {
    die("No available flights found for the selected route.");
}

$flight = $result->fetch_assoc();

function generatePNR($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $pnr = '';
    for ($i = 0; $i < $length; $i++) {
        $pnr .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $pnr;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process passenger details submission
    $passengers = [];
    for ($i = 1; $i <= $num_passengers; $i++) {
        $first_name = trim($_POST["first_name_$i"] ?? '');
        $last_name = trim($_POST["last_name_$i"] ?? '');
        $gender = $_POST["gender_$i"] ?? '';
        $age = intval($_POST["age_$i"] ?? 0);
        $email = trim($_POST["email_$i"] ?? '');
        $phone = trim($_POST["phone_$i"] ?? '');

        if (!$first_name || !$last_name || !$gender || $age < 1 || !$email) {
            $error_message = "Please fill in all required fields for passenger $i.";
            break;
        }

        $passengers[] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'gender' => $gender,
            'age' => $age,
            'email' => $email,
            'phone' => $phone
        ];
    }

    if (!$error_message) {
        // Generate unique PNR and ensure it does not exist
        do {
            $pnr = generatePNR();
            $check_sql = "SELECT id FROM bookings WHERE pnr = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $pnr);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
        } while ($check_result->num_rows > 0);

// Insert booking
$total_amount = $flight['price'] * $num_passengers;
$user_id = $_SESSION['user_id'] ?? null;
$insert_booking_sql = "INSERT INTO bookings (pnr, flight_id, total_amount, status, user_id) VALUES (?, ?, ?, 'Confirmed', ?)";
$insert_booking_stmt = $conn->prepare($insert_booking_sql);
$insert_booking_stmt->bind_param("sidi", $pnr, $flight['id'], $total_amount, $user_id);
if ($insert_booking_stmt->execute()) {
    $booking_id = $insert_booking_stmt->insert_id;

            // Insert passengers
$insert_passenger_sql = "INSERT INTO passengers (booking_id, first_name, last_name, gender, age, email, phone, seat_number, checked_in) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, 0)";
$insert_passenger_stmt = $conn->prepare($insert_passenger_sql);

foreach ($passengers as $p) {
    $insert_passenger_stmt->bind_param("isssiss", $booking_id, $p['first_name'], $p['last_name'], $p['gender'], $p['age'], $p['email'], $p['phone']);
    $insert_passenger_stmt->execute();
}

            // Redirect to booking confirmation page with PNR
            header("Location: booking-confirmation.php?pnr=" . urlencode($pnr));
            exit();
        } else {
            $error_message = "Failed to create booking. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header" style="margin-bottom: 20px;">
        <div class="container">
            <h1>Passenger Details</h1>
        </div>
    </section>

    <section class="passenger-details-section">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div class="flight-details-card" style="margin-bottom: 30px; padding: 15px; background: #fff; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 15px;">Flight Details</h2>
                <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
                <p><strong>From:</strong> <?php echo htmlspecialchars($flight['origin']); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($flight['destination']); ?></p>
                <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($flight['departure_time']); ?></p>
                <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($flight['arrival_time']); ?></p>
                <p><strong>Price per Passenger:</strong> <?php echo htmlspecialchars($flight['price']); ?> INR</p>
                <p><strong>Number of Passengers:</strong> <?php echo $num_passengers; ?></p>
                <p><strong>Total Amount:</strong> <?php echo $flight['price'] * $num_passengers; ?> INR</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error" style="color: red; margin-bottom: 20px;"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="" method="POST" style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 20px;">Enter Passenger Details</h2>
                <?php for ($i = 1; $i <= $num_passengers; $i++): ?>
                    <fieldset style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                        <legend style="font-weight: bold; padding: 0 10px;">Passenger <?php echo $i; ?></legend>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="first_name_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">First Name</label>
                            <input type="text" id="first_name_<?php echo $i; ?>" name="first_name_<?php echo $i; ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="last_name_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">Last Name</label>
                            <input type="text" id="last_name_<?php echo $i; ?>" name="last_name_<?php echo $i; ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="gender_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">Gender</label>
                            <select id="gender_<?php echo $i; ?>" name="gender_<?php echo $i; ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="age_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">Age</label>
                            <input type="number" id="age_<?php echo $i; ?>" name="age_<?php echo $i; ?>" min="1" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="email_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">Email</label>
                            <input type="email" id="email_<?php echo $i; ?>" name="email_<?php echo $i; ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="phone_<?php echo $i; ?>" style="display: block; margin-bottom: 5px;">Phone (optional)</label>
                            <input type="text" id="phone_<?php echo $i; ?>" name="phone_<?php echo $i; ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                    </fieldset>
                <?php endfor; ?>
                <div class="form-submit" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">Proceed</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
