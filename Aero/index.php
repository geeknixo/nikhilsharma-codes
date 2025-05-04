<?php
session_start();
$page_title = "Book Flights & Check-in Online";
include 'includes/db_connect.php';

// Fetch distinct origins and destinations for dropdowns
$origins_result = $conn->query("SELECT DISTINCT origin FROM flights ORDER BY origin ASC");
$destinations_result = $conn->query("SELECT DISTINCT destination FROM flights ORDER BY destination ASC");

$origins = [];
$destinations = [];

if ($origins_result && $origins_result->num_rows > 0) {
    while ($row = $origins_result->fetch_assoc()) {
        $origins[] = $row['origin'];
    }
}

if ($destinations_result && $destinations_result->num_rows > 0) {
    while ($row = $destinations_result->fetch_assoc()) {
        $destinations[] = $row['destination'];
    }
}

include 'includes/header.php';
?>

<main>
    
    <section class="hero">
        <div class="hero-content">
            
            <h1>Fly Smart, Fly AerospaceAirways</h1>
            <p>Book domestic and international flights at the best prices.</p>
        </div>
    </section>

    <section class="booking-form">
        <div class="container">
            <div class="form-card">
                <h2>Book Your Flight</h2>
                <div id="booking-form">
                    <div class="form-group">
                        <label for="from_location">From Location</label>
                        <select id="from_location" name="from_location" required>
                            <option value="">Select Origin</option>
                            <?php foreach ($origins as $origin): ?>
                                <option value="<?php echo htmlspecialchars($origin); ?>"><?php echo htmlspecialchars($origin); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="to_location">To Location</label>
                        <select id="to_location" name="to_location" required>
                            <option value="">Select Destination</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="num_passengers">Number of Passengers</label>
                        <input type="number" id="num_passengers" name="num_passengers" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Total Amount: </label>
                        <span id="total_amount">0.00</span> INR
                    </div>
                    <div class="form-submit">
                        <button type="button" id="book_now_btn" class="btn btn-primary">Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="frequently-visited-places" style="background-color: #fff0f6; padding: 50px 0;">
        <div class="container">
            <h2 style="color: #e91d64; text-align: center; margin-bottom: 40px;">Frequently Visited Places</h2>
            <div class="places-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <div class="place-card" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(233, 29, 100, 0.2); cursor: pointer; transition: transform 0.3s ease;">
                    <img src="assets/images/new york.jpg" alt="New York" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(233, 29, 100, 0.8)); color: white; padding: 15px; font-size: 1.2rem; font-weight: 700; text-align: center;">
                        New York
                    </div>
                </div>
                <div class="place-card" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(233, 29, 100, 0.2); cursor: pointer; transition: transform 0.3s ease;">
                    <img src="assets/images/london.jpg" alt="London" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(233, 29, 100, 0.8)); color: white; padding: 15px; font-size: 1.2rem; font-weight: 700; text-align: center;">
                        London
                    </div>
                </div>
                <div class="place-card" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(233, 29, 100, 0.2); cursor: pointer; transition: transform 0.3s ease;">
                    <img src="assets/images/paris.jpg" alt="Paris" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(233, 29, 100, 0.8)); color: white; padding: 15px; font-size: 1.2rem; font-weight: 700; text-align: center;">
                        Paris
                    </div>
                </div>
                <div class="place-card" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(233, 29, 100, 0.2); cursor: pointer; transition: transform 0.3s ease;">
                    <img src="assets/images/tokyo.jpg" alt="Tokyo" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(233, 29, 100, 0.8)); color: white; padding: 15px; font-size: 1.2rem; font-weight: 700; text-align: center;">
                        Tokyo
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services">
        <div class="container">
            <div class="services-grid" style="display: flex; gap: 20px; justify-content: center;">
                <div class="service-card" style="flex: 1;">
                    <div class="service-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Manage Booking</h3>
                    <p>View, modify or cancel your bookings online with ease.</p>
                    <a href="manage-booking.php" class="btn-link">Manage Now</a>
                </div>
                <div class="service-card" style="flex: 1;">
                    <div class="service-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>View History</h3>
                    <p>Access your booking history and past trips easily.</p>
                    <a href="user-bookings.php" class="btn-link">View History</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromSelect = document.getElementById('from_location');
    const toSelect = document.getElementById('to_location');
    const numPassengersInput = document.getElementById('num_passengers');
    const totalAmountSpan = document.getElementById('total_amount');
    const bookNowBtn = document.getElementById('book_now_btn');

    function updateTotalAmount() {
        console.log('Updating total amount');
        const from = fromSelect.value;
        const to = toSelect.value;
        const numPassengers = parseInt(numPassengersInput.value) || 1;
        console.log(`From: ${from}, To: ${to}, Passengers: ${numPassengers}`);

        if (from && to) {
            fetch(`get_flight_price.php?origin=${encodeURIComponent(from)}&destination=${encodeURIComponent(to)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Price data received:', data);
                    if (data.price) {
                        const total = data.price * numPassengers;
                        totalAmountSpan.textContent = total.toFixed(2);
                    } else {
                        totalAmountSpan.textContent = '0.00';
                    }
                })
                .catch((error) => {
                    console.error('Fetch error:', error);
                    totalAmountSpan.textContent = '0.00';
                });
        } else {
            totalAmountSpan.textContent = '0.00';
        }
    }

    bookNowBtn.addEventListener('click', function() {
        console.log('Book Now button clicked');
        const from = fromSelect.value;
        const to = toSelect.value;
        const numPassengers = numPassengersInput.value;
        console.log(`From: ${from}, To: ${to}, Passengers: ${numPassengers}`);

        if (!from) {
            alert('Please select a From Location.');
            return;
        }
        if (!to) {
            alert('Please select a To Location.');
            return;
        }
        if (!numPassengers || numPassengers < 1) {
            alert('Please enter a valid number of passengers.');
            return;
        }

        const url = `passenger-details.php?from_location=${encodeURIComponent(from)}&to_location=${encodeURIComponent(to)}&num_passengers=${encodeURIComponent(numPassengers)}`;
        console.log(`Redirecting to URL: ${url}`);

        window.location.href = url;
    });

    fromSelect.addEventListener('change', function() {
        const from = fromSelect.value;
        if (!from) {
            toSelect.innerHTML = '<option value="">Select Destination</option>';
            updateTotalAmount();
            return;
        }
        fetch(`get_destinations.php?origin=${encodeURIComponent(from)}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Select Destination</option>';
                data.forEach(dest => {
                    options += `<option value="${dest}">${dest}</option>`;
                });
                toSelect.innerHTML = options;
                updateTotalAmount();
            })
            .catch(() => {
                toSelect.innerHTML = '<option value="">Select Destination</option>';
                updateTotalAmount();
            });
    });

    toSelect.addEventListener('change', updateTotalAmount);
    numPassengersInput.addEventListener('input', updateTotalAmount);

    // Initial total amount update
    updateTotalAmount();
});
</script>

<?php include 'includes/footer.php'; ?>
