<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // --- MERGE GUEST TRANSACTIONS ---
            if (isset($_SESSION['guest_transactions']) && !empty($_SESSION['guest_transactions'])) {
                $guest_transactions = $_SESSION['guest_transactions'];
                $stmt_insert = $conn->prepare("INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");

                foreach ($guest_transactions as $t) {
                    $stmt_insert->execute([
                        $user['id'],
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

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(!empty($_SESSION['success'])) { echo "<p style='color:green;'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
<form method="post">
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
