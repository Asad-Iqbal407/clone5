<?php
include 'includes/config.php';
include 'includes/functions.php';

// Check if user is logged in to show different content
$isLoggedIn = is_logged_in();
$userName = $isLoggedIn ? ($_SESSION['user']['first_name'] ?? '') : '';
$userEmail = $isLoggedIn ? ($_SESSION['user']['email'] ?? '') : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>T4M Demo — Home</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="home">
  <!-- include header -->
  <?php include __DIR__.'/includes/header.php'; ?>

  <!-- The mega menu fragment is inside header.php and will be open on home -->
  <script>
    // Show mega menu on home page load
    document.addEventListener('DOMContentLoaded', function() {
      const menu = document.getElementById('megaMenu');
      if (menu) {
        menu.hidden = false;
      }
    });
  </script>

  <main class="container page-intro">
    <h2>Featured</h2>



    <!-- Repair Services Banner -->
    <div class="repair-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; border-radius: 16px; margin-bottom: 30px; text-align: center;">
      <h3 style="margin: 0 0 15px; font-size: 2rem; font-weight: 800;">🔧 Professional Device Repair Services</h3>
      <p style="margin: 0 0 25px; font-size: 1.1rem; opacity: 0.9;">Expert technicians • OEM parts • Same-day service • Lifetime warranty</p>
      <a href="repair.php" class="btn" style="background: white; color: #667eea; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block; transition: all 0.3s ease;">Get Your Device Fixed</a>
    </div>

    <div id="productGrid" class="products-grid"></div>
  </main>

  <!-- include footer -->
  <?php include __DIR__.'/includes/footer.php'; ?>

  <script src="assets/js/app.js"></script>
  <script>
    // Show some featured (bestseller) products on the home grid
    window.addEventListener('load', function(){
      const grid = document.getElementById('productGrid');
      if(!grid) return;
      // Placeholder for products - in a real app, this would fetch from database
      const featured = [
        {name: 'iPhone 15', price: '999€', image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'},
        {name: 'Samsung Galaxy S24', price: '899€', image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'},
        {name: 'AirPods Pro', price: '249€', image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c9eaef?w=300&h=300&fit=crop'},
        {name: 'MacBook Air', price: '1199€', image: 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=300&h=300&fit=crop'}
      ];
      renderProducts(grid, featured, 'Featured');
    });
  </script>
</body>
</html>