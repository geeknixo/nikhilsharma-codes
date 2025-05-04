<?php
session_start();
$page_title = "Contact Us";
include 'includes/db_connect.php';

$name = $email = $message = "";
$name_err = $email_err = $message_err = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }

    // If no errors, insert into database
    if (empty($name_err) && empty($email_err) && empty($message_err)) {
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_name, $param_email, $param_message);
            $param_name = $name;
            $param_email = $email;
            $param_message = $message;
            if ($stmt->execute()) {
                $success_message = "Thank you for contacting us. We will get back to you soon.";
                $name = $email = $message = "";
            } else {
                $success_message = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}

// Fetch reviews from database
$reviews = [];
$sql_reviews = "SELECT reviewer_name, review_text, rating FROM reviews ORDER BY review_date DESC LIMIT 5";
$result_reviews = $conn->query($sql_reviews);
if ($result_reviews && $result_reviews->num_rows > 0) {
    while ($row = $result_reviews->fetch_assoc()) {
        $reviews[] = $row;
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
        </div>
    </section>

    <section class="contact-form-section">
        <div class="container">
            <h2>Get in Touch</h2>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <form id="contact-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <span class="error"><?php echo $name_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="error"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    <span class="error"><?php echo $message_err; ?></span>
                </div>
                <div class="form-submit">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </section>

    <section class="reviews-section">
        <div class="container">
            <h2>What Our Customers Say</h2>
            <?php if (!empty($reviews)): ?>
<div class="reviews-grid">
    <?php 
    $profile_pics = ['p4.jpg', 'p2.jpg', 'p3.jpg', 'p1.jpg', 'p5.jpg'];
    $i = 0;
    foreach ($reviews as $review): 
        $pic = $profile_pics[$i] ?? 'profile-placeholder.jpg';
    ?>
        <div class="review-card">
            <img src="assets/images/<?php echo $pic; ?>" alt="Profile Picture" class="profile-pic" />
            <h3><?php echo htmlspecialchars($review['reviewer_name']); ?></h3>
            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
            <p>Rating: <?php echo str_repeat('★', intval($review['rating'])); ?></p>
        </div>
    <?php 
        $i++;
    endforeach; 
    ?>
</div>
            <?php else: ?>
                <p>No reviews available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.getElementById('contact-form').addEventListener('submit', function(event) {
    let valid = true;
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const message = document.getElementById('message');

    // Clear previous errors
    document.querySelectorAll('.error').forEach(el => el.textContent = '');

    if (!name.value.trim()) {
        document.querySelector('span.error[for="name"]').textContent = 'Please enter your name.';
        valid = false;
    }
    if (!email.value.trim()) {
        document.querySelector('span.error[for="email"]').textContent = 'Please enter your email.';
        valid = false;
    } else {
        const emailPattern = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
        if (!emailPattern.test(email.value.trim())) {
            document.querySelector('span.error[for="email"]').textContent = 'Please enter a valid email address.';
            valid = false;
        }
    }
    if (!message.value.trim()) {
        document.querySelector('span.error[for="message"]').textContent = 'Please enter your message.';
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
});
</script>

<style>
.page-header .container {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    font-size: 2.5rem;
    color: #e91d64;
    font-weight: 700;
}

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.review-card {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.review-card h3 {
    margin-bottom: 10px;
    color: #e91d64;
}

.review-card p {
    margin-bottom: 10px;
}

.error {
    color: red;
    font-size: 0.9rem;
}

.contact-form-section {
    background-color: white;
    padding: 40px 20px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.contact-form-section h2 {
    margin-bottom: 20px;
    color: #e91d64;
    text-align: center;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-submit {
    text-align: right;
}

.btn-primary {
    background-color: #e91d64;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary:hover {
    background-color: #c41854;
}
</style>
