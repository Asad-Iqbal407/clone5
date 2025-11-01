<?php
// admin/index.php - Admin panel main page
require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumbs"><a href="../index.php">Home</a> › Admin Panel</div>

<section class="card">
  <h1>Admin Panel</h1>
  <p>Welcome to the admin panel. Here you can manage products, categories, and other aspects of the website.</p>

  <div class="admin-nav">
    <a href="products.php" class="btn">Manage Products</a>
    <a href="users.php" class="btn">Manage Users</a>
    <a href="cart.php" class="btn">View Cart Items</a>
    <a href="categories.php" class="btn">Manage Categories</a>
    <a href="../logout.php" class="btn btn-red">Logout</a>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>