<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

include 'includes/db_connect.php';

$add_error = "";
$add_success = "";

// Handle add flight form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            $stmt->bind_param("sssssdis", $flight_number, $origin, $destination, $departure_time, $arrival_time, $price, $available_seats, $status);
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

include 'admin-header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Add Flight</h1>
        </div>
    </section>

    <section class="container">
        <?php if ($add_success): ?>
            <div class="alert alert-success"><?php echo $add_success; ?></div>
        <?php endif; ?>
        <?php if ($add_error): ?>
            <div class="alert alert-error"><?php echo $add_error; ?></div>
        <?php endif; ?>

        <form method="POST" action="admin-add-flight.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="flight_number">Flight Number</label>
                    <input type="text" id="flight_number" name="flight_number" required>
                </div>
                <div class="form-group">
                    <label for="origin">Origin</label>
                    <input type="text" id="origin" name="origin" required>
                </div>
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="destination" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="departure_time">Departure Time</label>
                    <input type="datetime-local" id="departure_time" name="departure_time" required>
                </div>
                <div class="form-group">
                    <label for="arrival_time">Arrival Time</label>
                    <input type="datetime-local" id="arrival_time" name="arrival_time" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (INR)</label>
                    <input type="number" step="0.01" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="available_seats">Available Seats</label>
                    <input type="number" id="available_seats" name="available_seats" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="On Time">On Time</option>
                        <option value="Delayed">Delayed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Landed">Landed</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Flight</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
