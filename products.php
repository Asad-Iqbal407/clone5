<?php
include 'includes/config.php';
include 'includes/functions.php';

// Connect to database for dynamic products
try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}

// Get category from URL parameter
$category = $_GET['cat'] ?? '';
$categoryTitle = htmlspecialchars($category);

// Sample products data for each category
$productsData = [
    'Smartphones' => [
        ['name' => 'iPhone 15 Pro Max', 'price' => '1299€', 'image' => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'],
        ['name' => 'Samsung Galaxy S24 Ultra', 'price' => '1199€', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'],
        ['name' => 'Google Pixel 8 Pro', 'price' => '999€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop'],
        ['name' => 'OnePlus 12', 'price' => '899€', 'image' => 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=300&h=300&fit=crop']
    ],
    'Cell Phones' => [
        ['name' => 'Samsung Galaxy A54', 'price' => '349€', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'],
        ['name' => 'iPhone SE', 'price' => '499€', 'image' => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'],
        ['name' => 'Nokia 3310', 'price' => '79€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop']
    ],
    'Refurbished' => [
        ['name' => 'Refurbished iPhone 14', 'price' => '699€', 'image' => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=300&h=300&fit=crop'],
        ['name' => 'Refurbished Samsung S23', 'price' => '599€', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop']
    ],
    'Landline' => [
        ['name' => 'Panasonic Cordless Phone', 'price' => '89€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => 'AT&T Landline Phone', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop']
    ],
    'Earbuds' => [
        ['name' => 'AirPods Pro', 'price' => '249€', 'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c9eaef?w=300&h=300&fit=crop'],
        ['name' => 'Samsung Galaxy Buds2 Pro', 'price' => '199€', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&h=300&fit=crop'],
        ['name' => 'Sony WF-1000XM5', 'price' => '299€', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop']
    ],
    'Microphones' => [
        ['name' => 'Blue Yeti USB', 'price' => '129€', 'image' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=300&h=300&fit=crop'],
        ['name' => 'Audio-Technica AT2020', 'price' => '99€', 'image' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=300&h=300&fit=crop']
    ],
    'Neckband' => [
        ['name' => 'Realme Buds Air 3', 'price' => '39€', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&h=300&fit=crop'],
        ['name' => 'OnePlus Bullets Z2', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop']
    ],
    'Speakers' => [
        ['name' => 'JBL GO 3', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop'],
        ['name' => 'Sony SRS-XB13', 'price' => '79€', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop']
    ],
    'Earphones' => [
        ['name' => 'Sony MDR-XB650BT', 'price' => '59€', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop'],
        ['name' => 'JBL T500', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&h=300&fit=crop']
    ],
    'Audio Cables' => [
        ['name' => 'USB-C to 3.5mm Cable', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'Lightning Cable', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Accessories' => [
        ['name' => 'Phone Case', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => 'Screen Protector', 'price' => '19€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop'],
        ['name' => 'Power Bank', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop']
    ],
    'Digital Pen' => [
        ['name' => 'Apple Pencil Pro', 'price' => '129€', 'image' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=300&h=300&fit=crop'],
        ['name' => 'Samsung S Pen', 'price' => '39€', 'image' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=300&h=300&fit=crop']
    ],
    'Smartwatches' => [
        ['name' => 'Apple Watch Series 9', 'price' => '399€', 'image' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?w=300&h=300&fit=crop'],
        ['name' => 'Samsung Galaxy Watch 6', 'price' => '349€', 'image' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?w=300&h=300&fit=crop']
    ],
    'Toys' => [
        ['name' => 'LEGO Mindstorms Robot', 'price' => '349€', 'image' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=300&h=300&fit=crop'],
        ['name' => 'Drone with Camera', 'price' => '199€', 'image' => 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=300&h=300&fit=crop']
    ],
    'Surveillance Camera' => [
        ['name' => 'Ring Indoor Cam', 'price' => '99€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'Wyze Cam v3', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=300&h=300&fit=crop']
    ],
    'Cigarette Lighter' => [
        ['name' => 'USB Rechargeable Lighter', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop'],
        ['name' => 'Windproof Lighter', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Prepaid SIM' => [
        ['name' => 'Vodafone Prepaid SIM', 'price' => '10€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop'],
        ['name' => 'Orange Prepaid SIM', 'price' => '10€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'Data SIM' => [
        ['name' => '5GB Data SIM', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop'],
        ['name' => '10GB Data SIM', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'eSIM' => [
        ['name' => 'Digital eSIM', 'price' => '5€', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop'],
        ['name' => 'International eSIM', 'price' => '20€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'Cases' => [
        ['name' => 'iPhone Silicone Case', 'price' => '35€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => 'Samsung Leather Case', 'price' => '45€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'OTG Adapter' => [
        ['name' => 'USB-C OTG Adapter', 'price' => '12€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'Lightning OTG Adapter', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Ringlight' => [
        ['name' => '20cm LED Ring Light', 'price' => '39€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop'],
        ['name' => 'Selfie Ring Light', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop']
    ],
    'Supports' => [
        ['name' => 'Phone Stand', 'price' => '18€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => 'Car Phone Mount', 'price' => '22€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'Glasses' => [
        ['name' => 'Blue Light Blocking Glasses', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300&h=300&fit=crop'],
        ['name' => 'Reading Glasses', 'price' => '19€', 'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300&h=300&fit=crop']
    ],
    'Lens' => [
        ['name' => 'Camera Lens 50mm', 'price' => '299€', 'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=300&h=300&fit=crop'],
        ['name' => 'Wide Angle Lens', 'price' => '149€', 'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=300&h=300&fit=crop']
    ],
    'Camera Lens' => [
        ['name' => 'Canon EF 50mm', 'price' => '199€', 'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=300&h=300&fit=crop'],
        ['name' => 'Nikon 35mm Lens', 'price' => '249€', 'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=300&h=300&fit=crop']
    ],
    'LCD Connector' => [
        ['name' => 'HDMI to LCD Cable', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'VGA to LCD Adapter', 'price' => '12€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Touch+Display' => [
        ['name' => '7" Touch Display', 'price' => '89€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => '10" Touch Screen', 'price' => '129€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'Batteries' => [
        ['name' => '18650 Battery Pack', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop'],
        ['name' => 'AA Battery 4-Pack', 'price' => '8€', 'image' => 'https://images.unsplash.com/photo-1609592806580-76e268ae25a9?w=300&h=300&fit=crop']
    ],
    'Cleaning Tools' => [
        ['name' => 'Screen Cleaning Kit', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'Phone Cleaning Brush', 'price' => '8€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Equipments' => [
        ['name' => 'Soldering Iron Kit', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300&h=300&fit=crop'],
        ['name' => 'Multimeter', 'price' => '35€', 'image' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300&h=300&fit=crop']
    ],
    'Glues' => [
        ['name' => 'Super Glue 5ml', 'price' => '5€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'Epoxy Glue', 'price' => '12€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Microscopes and Magnifiers' => [
        ['name' => 'Digital Microscope', 'price' => '89€', 'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=300&h=300&fit=crop'],
        ['name' => 'Magnifying Glass', 'price' => '15€', 'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=300&h=300&fit=crop']
    ],
    'Solder Wires' => [
        ['name' => 'Lead-free Solder', 'price' => '8€', 'image' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300&h=300&fit=crop'],
        ['name' => 'Rosin Core Solder', 'price' => '12€', 'image' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300&h=300&fit=crop']
    ],
    'Adapters' => [
        ['name' => 'USB-C Hub', 'price' => '39€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
        ['name' => 'HDMI Adapter', 'price' => '25€', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop']
    ],
    'Bag for Laptop' => [
        ['name' => 'Laptop Backpack', 'price' => '59€', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=300&h=300&fit=crop'],
        ['name' => 'Laptop Sleeve', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=300&h=300&fit=crop']
    ],
    'Display For Laptop' => [
        ['name' => '24" External Monitor', 'price' => '149€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'],
        ['name' => '27" 4K Monitor', 'price' => '299€', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop']
    ],
    'Keyboard' => [
        ['name' => 'Mechanical Keyboard', 'price' => '89€', 'image' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=300&h=300&fit=crop'],
        ['name' => 'Wireless Keyboard', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=300&h=300&fit=crop']
    ],
    'Mouse' => [
        ['name' => 'Gaming Mouse', 'price' => '59€', 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=300&h=300&fit=crop'],
        ['name' => 'Wireless Mouse', 'price' => '29€', 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=300&h=300&fit=crop']
    ],
    'Computer Speakers' => [
        ['name' => '2.1 Speaker System', 'price' => '79€', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop'],
        ['name' => 'Bluetooth Speakers', 'price' => '49€', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop']
    ]
];

// Always fetch from database first, then merge with hardcoded products
$dbProducts = [];
if (!empty($category)) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.name = ? ORDER BY p.id DESC");
    $stmt->execute([$category]);
    $dbProducts = $stmt->fetchAll();

    // Convert database products to the expected format
    $dbProducts = array_map(function($p) {
        return [
            'name' => $p['name'],
            'price' => '€' . number_format($p['price'], 2),
            'image' => $p['image'] ? 'uploads/' . $p['image'] : 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300&h=300&fit=crop'
        ];
    }, $dbProducts);
}

// Get hardcoded products
$hardcodedProducts = $productsData[$category] ?? [];

// Merge database products with hardcoded products
$products = array_merge($dbProducts, $hardcodedProducts);

include 'includes/header.php';
?>

<div class="breadcrumb container">
  <a class="crumb" href="<?=$BASE_URL?>">Home</a>
  <span class="sep">›</span>
  <span class="crumb-current"><?=$categoryTitle?></span>
</div>

<main class="container">
  <h1 class="page-title"><?=$categoryTitle?></h1>
  <p class="category-description">Discover our wide range of <?=$categoryTitle?> products</p>

  <!-- Repair Services Call-to-Action -->
  <?php if (in_array($category, ['Smartphones', 'Cell Phones', 'Refurbished', 'Earbuds', 'Smartwatches', 'Speakers', 'Microphones'])): ?>
    <div class="repair-cta" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px; text-align: center;">
      <h3 style="margin: 0 0 10px; font-size: 1.5rem;">🔧 Need Repair for Your <?=$categoryTitle?>?</h3>
      <p style="margin: 0 0 20px; opacity: 0.9;">Professional repair services with genuine parts and warranty</p>
      <a href="<?=$BASE_URL?>repair.php" class="btn" style="background: white; color: #667eea; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block;">Get Repair Quote</a>
    </div>
  <?php endif; ?>

  <div id="productGrid" class="products-grid"></div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  window.addEventListener('load', function(){
    const grid = document.getElementById('productGrid');
    if(!grid) return;

    const products = <?=json_encode($products)?>;
    renderProducts(grid, products, '<?=$categoryTitle?>');
  });
</script>