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
    // Check if user is logged in
    if (!is_logged_in()) {
        throw new Exception('You must be logged in to add items to cart');
    }

    // Get product data from POST
    $productName = trim($_POST['product_name'] ?? '');
    $productPrice = floatval($_POST['product_price'] ?? 0);
    $productImage = trim($_POST['product_image'] ?? '');

    if (empty($productName) || $productPrice <= 0) {
        throw new Exception('Invalid product data');
    }

    // Get or create cart session
    $sessionId = session_id();
    $userId = is_logged_in() ? $_SESSION['user']['id'] : null;

    // Check if cart session exists (for logged-in users, check by user_id OR session_id)
    $stmt = $pdo->prepare("SELECT id FROM cart_sessions WHERE session_id = ? OR (user_id = ? AND user_id IS NOT NULL)");
    $stmt->execute([$sessionId, $userId]);
    $cartSession = $stmt->fetch();

    if (!$cartSession) {
        // Create new cart session
        $stmt = $pdo->prepare("INSERT INTO cart_sessions (session_id, user_id) VALUES (?, ?)");
        $stmt->execute([$sessionId, $userId]);
        $cartSessionId = $pdo->lastInsertId();
    } else {
        $cartSessionId = $cartSession['id'];

        // If user just logged in and session doesn't have user_id, update it
        if ($userId && !$cartSession['user_id']) {
            $stmt = $pdo->prepare("UPDATE cart_sessions SET user_id = ? WHERE id = ?");
            $stmt->execute([$userId, $cartSessionId]);
        }
    }

    // Check if product already exists in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_session_id = ? AND product_name = ?");
    $stmt->execute([$cartSessionId, $productName]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + 1;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Add new item
        $stmt = $pdo->prepare("INSERT INTO cart_items (cart_session_id, product_name, product_price, product_image, quantity) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$cartSessionId, $productName, $productPrice, $productImage]);
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_session_id = ?");
    $stmt->execute([$cartSessionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = $result && isset($result['total']) ? (int)$result['total'] : 0;

    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'cart_count' => $cartCount
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>