<?php
session_start();
include 'db.php';

// Determine if user is logged in
$is_guest = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $is_guest = true;
    if (!isset($_SESSION['guest_transactions'])) {
        $_SESSION['guest_transactions'] = [];
    }
}

// Fetch transactions
if ($is_guest) {
    $transactions = $_SESSION['guest_transactions'];
} else {
    $stmt = $conn->query("SELECT * FROM transactions WHERE user_id=$user_id ORDER BY date DESC");
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
<h2>Welcome to Expense Tracker</h2>

<?php if($is_guest): ?>
<p>You're using the app as a guest. <a href="register.php">Register</a> or <a href="login.php">Login</a> to save your data permanently!</p>
<?php else: ?>
<a href="add_transaction.php">Add Transaction</a> | <a href="logout.php">Logout</a>
 | <a href="delete_account.php" 
   onclick="return confirm('Are you sure you want to delete your account? All data will be lost!');">
   Delete Account
</a>
<?php endif; ?>

<h3>Your Transactions</h3>
<table border="1">
<tr><th>Date</th><th>Type</th><th>Category</th><th>Amount</th></tr>
<?php foreach($transactions as $row): ?>
<tr>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['type']; ?></td>
<td><?php echo $row['category']; ?></td>
<td><?php echo $row['amount']; ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
