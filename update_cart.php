<?php
include 'includes/config.php';
include 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    $itemId = intval($_POST['item_id'] ?? 0);

    if (!$itemId) {
        throw new Exception('Invalid item ID');
    }

    // Get cart session
    $sessionId = session_id();
    $userId = is_logged_in() ? $_SESSION['user']['id'] : null;

    $stmt = $pdo->prepare("SELECT id FROM cart_sessions WHERE session_id = ? OR (user_id = ? AND user_id IS NOT NULL)");
    $stmt->execute([$sessionId, $userId]);
    $cartSession = $stmt->fetch();

    if (!$cartSession) {
        throw new Exception('Cart not found');
    }

    if ($action === 'update_quantity') {
        $newQuantity = intval($_POST['quantity'] ?? 0);

        if ($newQuantity < 0) {
            $newQuantity = 0;
        }

        if ($newQuantity === 0) {
            // Remove item if quantity is 0
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_session_id = ?");
            $stmt->execute([$itemId, $cartSession['id']]);
        } else {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_session_id = ?");
            $stmt->execute([$newQuantity, $itemId, $cartSession['id']]);
        }
    } elseif ($action === 'remove_item') {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_session_id = ?");
        $stmt->execute([$itemId, $cartSession['id']]);
    } else {
        throw new Exception('Invalid action');
    }

    // Get updated cart count and total
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count, SUM(quantity * product_price) as total FROM cart_items WHERE cart_session_id = ?");
    $stmt->execute([$cartSession['id']]);
    $result = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'cart_count' => $result['count'] ?? 0,
        'cart_total' => $result['total'] ?? 0
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>