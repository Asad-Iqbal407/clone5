<?php
// includes/config.php
session_start();

$BASE_URL = 'http://localhost/imobile/';

$db_host = 'localhost';
$db_name = 't4mshop';
$db_user = 'root';
$db_pass = ''; // XAMPP default (no password)

try {
  $pdo = new PDO(
    "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
    $db_user,
    $db_pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]
  );
} catch (PDOException $e) {
  die('DB connection failed: ' . $e->getMessage());
}

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
?>