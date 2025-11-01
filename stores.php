<?php include 'includes/header.php'; ?>

<div class="breadcrumb container">
  <a class="crumb" href="<?=$BASE_URL?>">Home</a>
  <span class="sep">›</span>
  <span class="crumb-current">Stores</span>
</div>

<main class="container">
  <h1 class="page-title">Our Stores</h1>
  <p class="page-description">Find our physical stores near you</p>

  <!-- Repair Services Banner -->
  <div class="repair-banner" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px; text-align: center;">
    <h3 style="margin: 0 0 10px; font-size: 1.5rem;">🔧 In-Store Repair Services Available</h3>
    <p style="margin: 0 0 20px; opacity: 0.9;">Visit any of our stores for immediate repair consultations</p>
    <a href="<?=$BASE_URL?>repair.php" class="btn" style="background: white; color: #48bb78; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block;">Schedule Repair</a>
  </div>

  <div class="stores-grid">
    <div class="store-card">
      <div class="store-image">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop" alt="Downtown Store">
      </div>
      <div class="store-info">
        <h3>Downtown Store</h3>
        <p class="store-address">123 Main Street, Downtown<br>City, State 12345</p>
        <p class="store-hours">Mon-Fri: 9AM-9PM<br>Sat-Sun: 10AM-8PM</p>
        <p class="store-phone">📞 (555) 123-4567</p>
      </div>
    </div>

    <div class="store-card">
      <div class="store-image">
        <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=400&h=250&fit=crop" alt="Mall Store">
      </div>
      <div class="store-info">
        <h3>Mall Store</h3>
        <p class="store-address">456 Shopping Plaza, Level 2<br>Mall City, State 67890</p>
        <p class="store-hours">Mon-Sun: 10AM-9PM</p>
        <p class="store-phone">📞 (555) 987-6543</p>
      </div>
    </div>

    <div class="store-card">
      <div class="store-image">
        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=250&fit=crop" alt="Airport Store">
      </div>
      <div class="store-info">
        <h3>Airport Store</h3>
        <p class="store-address">Terminal 3, Gate Area<br>Airport City, State 54321</p>
        <p class="store-hours">24/7 - Open 24 Hours</p>
        <p class="store-phone">📞 (555) 246-8135</p>
      </div>
    </div>

    <div class="store-card">
      <div class="store-image">
        <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=400&h=250&fit=crop" alt="Suburb Store">
      </div>
      <div class="store-info">
        <h3>Suburb Store</h3>
        <p class="store-address">789 Suburban Ave<br>Suburb City, State 13579</p>
        <p class="store-hours">Mon-Fri: 9AM-8PM<br>Sat-Sun: 10AM-7PM</p>
        <p class="store-phone">📞 (555) 369-2580</p>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>