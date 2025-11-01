<?php
// includes/header.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Fetch top-level categories to show in the drawer/grid
$topCats = $pdo->query("SELECT id, name, icon FROM categories WHERE parent_id IS NULL ORDER BY id")->fetchAll();
$subsStmt = $pdo->prepare("SELECT name FROM categories WHERE parent_id = ? ORDER BY id LIMIT 12");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>T4M Demo</title>
  <link rel="stylesheet" href="<?=$BASE_URL?>assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script>window.BASE_URL = "<?=e($BASE_URL)?>";</script>
  <script>
    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
      fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
          const cartBadge = document.getElementById('cartCount');
          if (cartBadge) {
            cartBadge.textContent = data.cart_count;
          }
        })
        .catch(error => console.error('Error loading cart count:', error));
    });
  </script>
</head>
<body>
<!-- Header (fragment) -->
<header class="site-header">
  <div class="container nav">
    <div class="nav-left">
      <button class="pill pill-orange" id="btnAllCategories" aria-expanded="false">
        <span class="burger"><span></span></span>
        All Categories
        <span class="chev">▾</span>
      </button>

      <a class="pill" href="<?=$BASE_URL?>index.php" data-nav="new">New</a>
      <a class="pill" href="<?=$BASE_URL?>bestsellers.php" data-nav="bestsellers">Bestsellers</a>
      <a class="pill pill-has-caret" href="<?=$BASE_URL?>repair.php" data-nav="repair">Repair <span class="chev">▾</span></a>
      <a class="pill pill-red" href="<?=$BASE_URL?>sale.php" data-nav="sale">Sale</a>
    </div>

    <div class="nav-right">
      <a class="link-store" href="<?=$BASE_URL?>stores.php" data-nav="stores">
        <span class="store-icon"></span> Stores
      </a>

      <?php if (is_logged_in()): ?>
        <span class="hello">Hi, <?=e($_SESSION['user']['first_name'] ?? 'User')?></span>
        <a class="link-account" href="<?=$BASE_URL?>logout.php" data-nav="account">Logout</a>
      <?php else: ?>
        <a class="link-account" href="<?=$BASE_URL?>login.php" data-nav="account">Login</a>
        <a class="link-account" href="<?=$BASE_URL?>register.php" data-nav="account">Register</a>
      <?php endif; ?>

      <a class="cart" href="<?=$BASE_URL?>cart.php" aria-label="Cart">
        <span class="cart-bag"></span>
        <span class="cart-badge" id="cartCount">0</span>
      </a>
    </div>
  </div>
</header>

<!-- Mega menu (hidden by default) -->
<section class="mega container" id="megaMenu" hidden>
  <div class="grid-3">
    <!-- Smartphones -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="18" y="6" width="28" height="52" rx="4" ry="4" fill="none" stroke="#111" stroke-width="3"/><rect x="23" y="13" width="18" height="38" fill="#111" opacity=".05"/><circle cx="32" cy="50" r="2" fill="#111"/></svg>
      </div>
      <h3>Smartphones/Cell Phones</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Smartphones">› Smartphones</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Cell%20Phones">› Cell Phones</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Refurbished">› Refurbished</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Landline">› Landline</a></li>
      </ul>
    </article>

    <!-- Sound & Audio -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="8" y="12" width="20" height="40" fill="none" stroke="#111" stroke-width="3"/><rect x="36" y="12" width="20" height="40" fill="none" stroke="#111" stroke-width="3"/><circle cx="18" cy="42" r="6" fill="none" stroke="#111" stroke-width="3"/><circle cx="18" cy="24" r="3" fill="#111"/><circle cx="46" cy="42" r="6" fill="none" stroke="#111" stroke-width="3"/><circle cx="46" cy="24" r="3" fill="#111"/></svg>
      </div>
      <h3>Sound & Audio</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Earbuds">› Earbuds</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Microphones">› Microphones</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Neckband">› Neckband</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Speakers">› Speakers</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Earphones">› Earphones</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Audio%20Cables">› Audio Cables</a></li>
      </ul>
    </article>

    <!-- Gadgets -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="22" y="16" width="20" height="32" rx="4" fill="none" stroke="#111" stroke-width="3"/><rect x="26" y="22" width="12" height="20" fill="none" stroke="#111" stroke-width="3"/><rect x="22" y="6" width="20" height="8" rx="2" fill="none" stroke="#111" stroke-width="3"/><rect x="22" y="48" width="20" height="10" rx="2" fill="none" stroke="#111" stroke-width="3"/></svg>
      </div>
      <h3>Gadgets</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Accessories">› Accessories</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Digital%20Pen">› Digital Pen</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Smartwatches">› Smartwatches</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Toys">› Toys</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Surveillance%20Camera">› Surveillance Camera</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Cigarette%20Lighter">› Cigarette Lighter</a></li>
      </ul>
    </article>

    <!-- SIM Cards -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><path d="M18 10h20l8 8v36H18z" fill="none" stroke="#111" stroke-width="3"/><rect x="22" y="28" width="20" height="18" fill="none" stroke="#111" stroke-width="3"/><line x1="22" y1="34" x2="42" y2="34" stroke="#111" stroke-width="3"/></svg>
      </div>
      <h3>SIM Cards</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Prepaid%20SIM">› Prepaid</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Data%20SIM">› Data SIM</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=eSIM">› eSIM</a></li>
      </ul>
    </article>

    <!-- Accessories -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="14" y="10" width="36" height="44" rx="6" fill="none" stroke="#111" stroke-width="3"/><rect x="20" y="16" width="24" height="30" fill="#111" opacity=".05"/></svg>
      </div>
      <h3>Accessories</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Cases">› Cases</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=OTG%20Adapter">› OTG Adapter</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Ringlight">› Ringlight</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Supports">› Supports</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Glasses">› Glasses</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Car%20Accessories" class="view-more">› View More</a></li>
      </ul>
    </article>

    <!-- Components -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="20" y="20" width="24" height="24" fill="none" stroke="#111" stroke-width="3"/><g stroke="#111" stroke-width="3"><line x1="12" y1="24" x2="20" y2="24"/><line x1="12" y1="32" x2="20" y2="32"/><line x1="12" y1="40" x2="20" y2="40"/><line x1="44" y1="24" x2="52" y2="24"/><line x1="44" y1="32" x2="52" y2="32"/><line x1="44" y1="40" x2="52" y2="40"/></g></svg>
      </div>
      <h3>Components</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Lens">› Lens</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Camera%20Lens">› Camera Lens</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=LCD%20Connector">› LCD Connector</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Touch+Display">› Touch+Display</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Batteries">› Batteries</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=More%20Components" class="view-more">› View More</a></li>
      </ul>
    </article>

    <!-- Tools -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><path d="M26 40l16-16 4 4-16 16z" fill="none" stroke="#111" stroke-width="3"/><path d="M20 28l8 8" stroke="#111" stroke-width="3"/><path d="M18 50l8-8" stroke="#111" stroke-width="3"/></svg>
      </div>
      <h3>Tools</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Cleaning%20Tools">› Cleaning Tools</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Equipments">› Equipments</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Glues">› Glues</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Microscopes%20and%20Magnifiers">› Microscopes and Magnifiers</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Solder%20Wires">› Solder Wires</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=More%20Tools" class="view-more">› View More</a></li>
      </ul>
    </article>

    <!-- Computing -->
    <article class="cat">
      <div class="cat-icon">
        <svg viewBox="0 0 64 64" aria-hidden="true"><rect x="10" y="16" width="44" height="28" rx="2" fill="none" stroke="#111" stroke-width="3"/><rect x="28" y="44" width="8" height="6" fill="#111"/><rect x="22" y="50" width="20" height="4" fill="#111"/></svg>
      </div>
      <h3>Computing</h3>
      <ul class="links">
        <li><a href="<?=$BASE_URL?>products.php?cat=Adapters">› Adapters</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Bag%20for%20Laptop">› Bag for Laptop</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Display%20For%20Laptop">› Display For Laptop</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Keyboard">› Keyboard</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Mouse">› Mouse</a></li>
        <li><a href="<?=$BASE_URL?>products.php?cat=Computer%20Speakers" class="view-more">› View More</a></li>
      </ul>
    </article>
  </div>
</section>

<main class="page wrap">