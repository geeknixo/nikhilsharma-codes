<?php
session_start();
$page_title = "Login";
include 'includes/header.php';
include 'includes/db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (!$password) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $first_name, $last_name, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start session
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $first_name . ' ' . $last_name;

                // Redirect to intended page after login if provided
                $redirect = $_GET['redirect'] ?? 'index.php';
                header("Location: " . $redirect);
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Aerospace Airways</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            background-color: #f5f5f5;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #e91d64;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #e91d64;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background-color: #c41854;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .link-text {
            text-align: center;
            margin-top: 15px;
        }
        .link-text a {
            color: #e91d64;
            text-decoration: none;
        }
        .link-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Login</button>
        </form>
        <div class="link-text">
            <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
        </div>
    </div>
</body>
</html>
