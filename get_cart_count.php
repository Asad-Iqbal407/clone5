<?php
include 'includes/config.php';
include 'includes/functions.php';

header('Content-Type: application/json');

// Get cart session
$sessionId = session_id();
$userId = is_logged_in() ? $_SESSION['user']['id'] : null;

// Find cart session
$stmt = $pdo->prepare("SELECT id FROM cart_sessions WHERE session_id = ? OR (user_id = ? AND user_id IS NOT NULL)");
$stmt->execute([$sessionId, $userId]);
$cartSession = $stmt->fetch();

$cartCount = 0;

if ($cartSession) {
    // Get cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_session_id = ?");
    $stmt->execute([$cartSession['id']]);
    $result = $stmt->fetch();
    $cartCount = $result['total'] ?? 0;
}

echo json_encode(['cart_count' => $cartCount]);
?>