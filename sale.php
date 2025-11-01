<?php include __DIR__.'/includes/header.php'; ?>

<div class="breadcrumb container">
  <a class="crumb" href="<?=$BASE_URL?>">Home</a>
  <span class="sep">›</span>
  <span class="crumb-current">Sale</span>
</div>

<main class="container">
  <h1 class="page-title">Sale</h1>

  <!-- Repair Services Banner -->
  <div class="repair-banner" style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px; text-align: center;">
    <h3 style="margin: 0 0 10px; font-size: 1.5rem;">🔧 Repair Services on Sale!</h3>
    <p style="margin: 0 0 20px; opacity: 0.9;">Get 20% off on all repair services this month</p>
    <a href="<?=$BASE_URL?>repair.php" class="btn" style="background: white; color: #e53e3e; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block;">Book Repair Now</a>
  </div>

  <div id="productGrid" class="products-grid"></div>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>

<script>
  window.addEventListener('load', function(){
    const grid = document.getElementById('productGrid');
    // Placeholder for sale products - in a real app, this would fetch from database
    const saleProducts = [
      {name: 'iPhone 14', price: '699€', originalPrice: '899€', image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'},
      {name: 'Samsung Galaxy S23', price: '599€', originalPrice: '799€', image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'},
      {name: 'AirPods 3rd Gen', price: '149€', originalPrice: '179€', image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c9eaef?w=300&h=300&fit=crop'},
      {name: 'MacBook Air M1', price: '899€', originalPrice: '1099€', image: 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=300&h=300&fit=crop'},
      {name: 'iPad 9th Gen', price: '349€', originalPrice: '429€', image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'},
      {name: 'Sony WH-1000XM4', price: '249€', originalPrice: '349€', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop'}
    ];
    renderProducts(grid, saleProducts, 'Sale');
  });
</script>