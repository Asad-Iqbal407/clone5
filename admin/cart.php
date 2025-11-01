<?php
// admin/cart.php - View cart items
require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/../includes/functions.php';

// Fetch all cart sessions with items
$cart_sessions = $pdo->query("
    SELECT
        cs.id,
        cs.session_id,
        cs.user_id,
        cs.created_at,
        cs.updated_at,
        u.first_name,
        u.last_name,
        u.email,
        COUNT(ci.id) as item_count,
        SUM(ci.quantity) as total_quantity,
        SUM(ci.product_price * ci.quantity) as total_value
    FROM cart_sessions cs
    LEFT JOIN users u ON cs.user_id = u.id
    LEFT JOIN cart_items ci ON cs.id = ci.cart_session_id
    GROUP BY cs.id
    ORDER BY cs.updated_at DESC
")->fetchAll();

// Get detailed cart items for a specific session if requested
$cart_items = [];
if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
    $stmt = $pdo->prepare("
        SELECT ci.*, cs.session_id, cs.user_id, u.first_name, u.last_name, u.email
        FROM cart_items ci
        JOIN cart_sessions cs ON ci.cart_session_id = cs.id
        LEFT JOIN users u ON cs.user_id = u.id
        WHERE cs.session_id = ?
        ORDER BY ci.created_at DESC
    ");
    $stmt->execute([$session_id]);
    $cart_items = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumbs"><a href="../index.php">Home</a> › <a href="index.php">Admin Panel</a> › Cart Items</div>

<section class="card">
  <h1>Shopping Cart Management</h1>
  <p>View all active shopping carts and their contents.</p>

  <?php if (isset($_GET['session_id'])): ?>
    <a href="cart.php" class="btn">← Back to Cart Sessions</a>
    <h2>Cart Items for Session: <?=$_GET['session_id']?></h2>

    <?php if ($cart_items): ?>
      <div class="cart-summary">
        <p><strong>User:</strong> <?=$cart_items[0]['first_name'] ? $cart_items[0]['first_name'] . ' ' . $cart_items[0]['last_name'] . ' (' . $cart_items[0]['email'] . ')' : 'Guest User'?></p>
        <p><strong>Session ID:</strong> <?=$cart_items[0]['session_id']?></p>
        <p><strong>User ID:</strong> <?=$cart_items[0]['user_id'] ?: 'N/A'?></p>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Image</th>
            <th>Added</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = 0;
          foreach ($cart_items as $item):
            $subtotal = $item['product_price'] * $item['quantity'];
            $total += $subtotal;
          ?>
            <tr>
              <td><?=$item['product_name']?></td>
              <td>$<?=number_format($item['product_price'], 2)?></td>
              <td><?=$item['quantity']?></td>
              <td>$<?=number_format($subtotal, 2)?></td>
              <td><?php if ($item['product_image']): ?><img src="../uploads/<?=$item['product_image']?>" alt="Product image" style="max-width:50px;"><?php endif; ?></td>
              <td><?=$item['created_at']?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3">Total</th>
            <th>$<?=number_format($total, 2)?></th>
            <th colspan="2"></th>
          </tr>
        </tfoot>
      </table>
    <?php else: ?>
      <p>No items found in this cart session.</p>
    <?php endif; ?>

  <?php else: ?>
    <h2>Active Cart Sessions</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Session ID</th>
          <th>User</th>
          <th>Email</th>
          <th>Items (Qty)</th>
          <th>Total Value</th>
          <th>Last Updated</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart_sessions as $session): ?>
          <tr>
            <td><?=$session['session_id']?></td>
            <td><?=$session['first_name'] ? $session['first_name'] . ' ' . $session['last_name'] : 'Guest'?></td>
            <td><?=$session['email'] ?: 'N/A'?></td>
            <td><?=$session['item_count']?> (<?=$session['total_quantity']?>)</td>
            <td>$<?=number_format($session['total_value'] ?: 0, 2)?></td>
            <td><?=$session['updated_at']?></td>
            <td>
              <a href="?session_id=<?=$session['session_id']?>" class="btn btn-small">View Items</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>