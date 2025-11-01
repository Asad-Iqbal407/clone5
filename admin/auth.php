<?php
// admin/auth.php - Admin authentication check
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

function require_admin() {
    if (!is_logged_in() || !isset($_SESSION['user']['is_admin']) || $_SESSION['user']['is_admin'] != 1) {
        header('Location: ' . $BASE_URL . 'admin/login.php');
        exit;
    }
}

// Update session with admin status if logged in
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user']['is_admin'] = $user['is_admin'];
    }
}

// Call require_admin() at the top of every admin page to ensure only admins can access
require_admin();
?>