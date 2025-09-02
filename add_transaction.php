<?php
session_start();
require 'db.php';

$error = "";
$success = "";

// Determine if user is logged in or guest
$is_guest = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $is_guest = true;
    if (!isset($_SESSION['guest_transactions'])) {
        $_SESSION['guest_transactions'] = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    if (empty($type) || empty($category) || empty($amount)) {
        $error = "Please fill in all required fields.";
    } else {
        if ($is_guest) {
            // Save transaction in session for guests
            $_SESSION['guest_transactions'][] = [
                'type' => $type,
                'category' => $category,
                'amount' => $amount,
                'description' => $description,
                'date' => date('Y-m-d H:i:s')
            ];
            $success = "Transaction added temporarily (guest mode)!";
        } else {
            // Save transaction in database for logged-in users
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, category, amount, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $type, $category, $amount, $description]);
            $success = "Transaction added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Transaction</title>
</head>
<body>
<h2>Add Transaction</h2>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <label>Type:</label>
    <select name="type" required>
        <option value="">Select</option>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
    </select><br><br>

    <label>Category:</label>
    <input type="text" name="category" required><br><br>

    <label>Amount:</label>
    <input type="number" step="0.01" name="amount" required><br><br>

    <label>Description:</label>
    <input type="text" name="description"><br><br>

    <button type="submit">Add Transaction</button>
</form>

<p><a href="index.php">Go back to Dashboard</a></p>
</body>
</html>
