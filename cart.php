<?php
include 'includes/config.php';
include 'includes/functions.php';

// Get cart session
$sessionId = session_id();
$userId = is_logged_in() ? $_SESSION['user']['id'] : null;

// Find cart session
$stmt = $pdo->prepare("SELECT id FROM cart_sessions WHERE session_id = ? OR (user_id = ? AND user_id IS NOT NULL)");
$stmt->execute([$sessionId, $userId]);
$cartSession = $stmt->fetch();

$cartItems = [];
$total = 0;

if ($cartSession) {
    // Get cart items
    $stmt = $pdo->prepare("
        SELECT id, product_name, product_price, product_image, quantity
        FROM cart_items
        WHERE cart_session_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$cartSession['id']]);
    $cartItems = $stmt->fetchAll();

    // Calculate total
    foreach ($cartItems as $item) {
        $total += $item['product_price'] * $item['quantity'];
    }
}

include 'includes/header.php';
?>

<div class="breadcrumb container">
  <a class="crumb" href="<?=$BASE_URL?>">Home</a>
  <span class="sep">›</span>
  <span class="crumb-current">Shopping Cart</span>
</div>

<main class="container">
  <h1 class="page-title">Shopping Cart</h1>

  <?php if (empty($cartItems)): ?>
    <div class="empty-cart">
      <h2>Your cart is empty</h2>
      <p>Add some products to get started!</p>
      <a href="<?=$BASE_URL?>" class="btn">Continue Shopping</a>
    </div>
  <?php else: ?>
    <div class="cart-content">
      <div class="cart-items">
        <?php foreach ($cartItems as $item): ?>
          <div class="cart-item">
            <div class="item-image">
              <img src="<?=$item['product_image'] ?: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=100&h=100&fit=crop'?>" alt="<?=$item['product_name']?>">
            </div>
            <div class="item-details">
              <h3><?=$item['product_name']?></h3>
              <p class="item-price">€<?=$item['product_price']?></p>
            </div>
            <div class="item-quantity">
              <button class="qty-btn" onclick="updateQuantity(<?=$item['id']?>, <?=$item['quantity']-1?>)">-</button>
              <span class="qty"><?=$item['quantity']?></span>
              <button class="qty-btn" onclick="updateQuantity(<?=$item['id']?>, <?=$item['quantity']+1?>)">+</button>
            </div>
            <div class="item-total">
              <p>€<?=$item['product_price'] * $item['quantity']?></p>
            </div>
            <div class="item-remove">
              <button class="remove-btn" onclick="removeItem(<?=$item['id']?>)">×</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="cart-summary">
        <div class="summary-row">
          <span>Subtotal:</span>
          <span>€<?=$total?></span>
        </div>
        <div class="summary-row">
          <span>Shipping:</span>
          <span>Free</span>
        </div>
        <div class="summary-row total">
          <span>Total:</span>
          <span>€<?=$total?></span>
        </div>
        <button class="btn checkout-btn">Proceed to Checkout</button>
        <a href="<?=$BASE_URL?>" class="continue-shopping">Continue Shopping</a>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  function updateQuantity(itemId, newQty) {
    if (newQty < 1) return;
    // In a real app, this would make an AJAX call to update the cart
    console.log('Update quantity for item', itemId, 'to', newQty);
  }

  function removeItem(itemId) {
    if (confirm('Remove this item from cart?')) {
      // In a real app, this would make an AJAX call to remove the item
      console.log('Remove item', itemId);
    }
  }
</script>