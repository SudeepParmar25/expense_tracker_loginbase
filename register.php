<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            if ($stmt->execute([$email, $hashed])) {
                // Get new user ID
                $new_user_id = $conn->lastInsertId();

                // --- GUEST TRANSACTION INTEGRATION ---
                if (isset($_SESSION['guest_transactions']) && !empty($_SESSION['guest_transactions'])) {
                    $guest_transactions = $_SESSION['guest_transactions'];
                    $stmt_insert = $conn->prepare("INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");

                    foreach ($guest_transactions as $t) {
                        $stmt_insert->execute([
                            $new_user_id,
                            $t['type'],
                            $t['category'],
                            $t['amount'],
                            $t['description'],
                            $t['date']
                        ]);
                    }

                    // Clear guest transactions
                    unset($_SESSION['guest_transactions']);
                }

                $_SESSION['success'] = "Registration successful. Please login.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Error registering user.";
            }
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
