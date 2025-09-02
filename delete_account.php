<?php
session_start();
require 'db.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Optional: delete all transactions of this user
$stmt = $conn->prepare("DELETE FROM transactions WHERE user_id = ?");
$stmt->execute([$user_id]);

// Delete the user account
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
