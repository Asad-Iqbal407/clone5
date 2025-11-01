<?php include __DIR__.'/includes/header.php'; ?>

<div class="breadcrumb container">
  <a class="crumb" href="<?=$BASE_URL?>">Home</a>
  <span class="sep">›</span>
  <span class="crumb-current">Bestsellers</span>
</div>

<main class="container">
  <h1 class="page-title">Bestsellers</h1>

  <!-- Repair Services Banner -->
  <div class="repair-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px; text-align: center;">
    <h3 style="margin: 0 0 10px; font-size: 1.5rem;">🔧 Repair Services for Your Bestselling Devices</h3>
    <p style="margin: 0 0 20px; opacity: 0.9;">Professional repair services for iPhone, Samsung, Sony and more</p>
    <a href="<?=$BASE_URL?>repair.php" class="btn" style="background: white; color: #667eea; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block;">Get Repair Service</a>
  </div>

  <div id="productGrid" class="products-grid"></div>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>

<script>
  window.addEventListener('load', function(){
    const grid = document.getElementById('productGrid');
    // Placeholder for bestseller products - in a real app, this would fetch from database
    const bestsellers = [
      {name: 'iPhone 15 Pro', price: '1199€', image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'},
      {name: 'Samsung Galaxy S24 Ultra', price: '1299€', image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'},
      {name: 'MacBook Pro 16"', price: '2499€', image: 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=300&h=300&fit=crop'},
      {name: 'Sony WH-1000XM5', price: '399€', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop'},
      {name: 'iPad Air', price: '599€', image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'},
      {name: 'AirPods Max', price: '549€', image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c9eaef?w=300&h=300&fit=crop'}
    ];
    renderProducts(grid, bestsellers, 'Bestsellers');
  });
</script>