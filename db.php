<?php
$host = getenv('DB_HOST');       // e.g. your-render-mysql-host
$db   = getenv('DB_NAME');       // e.g. expense_tracker
$user = getenv('DB_USER');       // your db username
$pass = getenv('DB_PASS');       // your db password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
