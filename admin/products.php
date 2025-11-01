<?php
// admin/products.php - Manage products
require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$success = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();

        if (isset($_POST['delete'])) {
            $product_id = (int)$_POST['product_id'];
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            set_flash('success', 'Product deleted successfully.');
            header('Location: products.php');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category_name = trim($_POST['category_id'] ?? '');

        if (!$name) $errors[] = 'Product name is required.';
        if ($price <= 0) $errors[] = 'Price must be greater than 0.';
        if (!$category_name) $errors[] = 'Category is required.';

        if (!$errors) {
            $image = handle_image_upload($_FILES['image'] ?? []);

            // Get category_id from category name
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$category_name]);
            $category = $stmt->fetch();
            $category_id = $category ? $category['id'] : 0;

            if (!$category_id) {
                $errors[] = 'Invalid category selected.';
            } else {
                if (isset($_POST['product_id'])) {
                    // Update
                    $product_id = (int)$_POST['product_id'];
                    if ($image) {
                        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
                        $stmt->execute([$name, $description, $price, $category_id, $image, $product_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ? WHERE id = ?");
                        $stmt->execute([$name, $description, $price, $category_id, $product_id]);
                    }
                    set_flash('success', 'Product updated successfully.');
                } else {
                    // Add
                    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $description, $price, $category_id, $image]);
                    set_flash('success', 'Product added successfully.');
                }
            }
            header('Location: products.php');
            exit;
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

// Fetch products
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_product = $stmt->fetch();
}

// For edit form, get the category name for selected value
if ($edit_product) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$edit_product['category_id']]);
    $cat = $stmt->fetch();
    $edit_product['category_name'] = $cat ? $cat['name'] : '';
}

// Fetch main categories and subcategories for hierarchical dropdown
$mainCategories = $pdo->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name")->fetchAll();
$subCategories = $pdo->query("
    SELECT
        c.id,
        c.name as sub_name,
        p.id as parent_id,
        p.name as parent_name
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    WHERE c.parent_id IS NOT NULL
    ORDER BY p.name, c.name
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumbs"><a href="../index.php">Home</a> › <a href="index.php">Admin Panel</a> › Products</div>

<section class="card">
  <h1>Manage Products</h1>

  <?php if ($success): ?>
    <div class="note" style="color:#4caf50"><?=$success?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul class="note" style="color:#e53935"><?php foreach ($errors as $e){ echo "<li>".e($e)."</li>"; } ?></ul>
  <?php endif; ?>

  <a href="?add=1" class="btn">Add New Product</a>

  <?php if (isset($_GET['add']) || $edit_product): ?>
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 24px; margin: 20px 0;">
      <h2 style="margin: 0 0 20px 0; color: #333; font-size: 1.5em;"><?=$edit_product ? 'Edit Product' : 'Add New Product'?></h2>
      <form method="post" enctype="multipart/form-data" data-validate style="max-width: none;">
        <?php csrf_field(); ?>
        <?php if ($edit_product): ?>
          <input type="hidden" name="product_id" value="<?=$edit_product['id']?>">
        <?php endif; ?>

        <div style="background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div style="display: flex; flex-direction: column;">
              <label for="product-name" style="font-weight: 600; margin-bottom: 6px; color: #495057;">Product Name *</label>
              <input type="text" id="product-name" name="name" required
                     value="<?=e($edit_product['name'] ?? $_POST['name'] ?? '')?>"
                     placeholder="Enter product name" style="padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="display: flex; flex-direction: column;">
              <label for="product-price" style="font-weight: 600; margin-bottom: 6px; color: #495057;">Price *</label>
              <div style="position: relative;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; font-weight: 600;">$</span>
                <input type="number" id="product-price" name="price" step="0.01" required
                       value="<?=e($edit_product['price'] ?? $_POST['price'] ?? '')?>"
                       placeholder="0.00" min="0" style="padding: 10px 10px 10px 24px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; width: 100%;">
              </div>
            </div>

            <div style="display: flex; flex-direction: column;">
              <label for="product-category" style="font-weight: 600; margin-bottom: 6px; color: #495057;">Category *</label>
              <select name="category_id" id="product-category" required style="padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background: white;">
                <option value="">Select Category</option>
                <?php
                $categoriesData = [
                    "Smartphones/Cell Phones" => ["Smartphones", "Cell Phones", "Refurbished", "Landline"],
                    "Sound & Audio" => ["Earbuds", "Microphones", "Neckband", "Speakers", "Earphones", "Audio Cables"],
                    "Gadgets" => ["Accessories", "Digital Pen", "Smartwatches", "Toys", "Surveillance Camera", "Cigarette Lighter"],
                    "SIM Cards" => ["Prepaid", "Data SIM", "eSIM"],
                    "Accessories" => ["Cases", "OTG Adapter", "Ringlight", "Supports", "Glasses"],
                    "Components" => ["Lens", "Camera Lens", "LCD Connector", "Touch+Display", "Batteries"],
                    "Tools" => ["Cleaning Tools", "Equipments", "Glues", "Microscopes and Magnifiers", "Solder Wires"],
                    "Computing" => ["Adapters", "Bag for Laptop", "Display For Laptop", "Keyboard", "Mouse"]
                ];

                foreach ($categoriesData as $mainCat => $subs):
                ?>
                  <optgroup label="<?=$mainCat?>">
                    <?php foreach ($subs as $sub): ?>
                      <option value="<?=$sub?>" <?=(($edit_product['category_name'] ?? '') === $sub) ? 'selected' : ''?>><?=$sub?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
            </div>

            <div style="display: flex; flex-direction: column;">
              <label for="product-image" style="font-weight: 600; margin-bottom: 6px; color: #495057;">Product Image</label>
              <div style="border: 2px dashed #dee2e6; border-radius: 6px; padding: 20px; text-align: center; background: #f8f9fa;">
                <input type="file" id="product-image" name="image" accept="image/*" style="display: none;">
                <label for="product-image" style="cursor: pointer; color: #6c757d;">
                  <i class="fas fa-cloud-upload-alt" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                  <span style="font-weight: 500;">Choose Image</span>
                </label>
                <div style="font-size: 12px; color: #6c757d; margin-top: 8px;">Supported formats: JPG, PNG, WEBP (Max: 5MB)</div>
              </div>
              <?php if ($edit_product && $edit_product['image']): ?>
                <div style="margin-top: 12px; text-align: center;">
                  <img src="../uploads/<?=$edit_product['image']?>" alt="Current image" style="max-width: 100px; max-height: 100px; border: 1px solid #dee2e6; border-radius: 4px;">
                  <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">Current Image</div>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div style="margin-top: 20px;">
            <label for="product-description" style="font-weight: 600; margin-bottom: 6px; color: #495057; display: block;">Description</label>
            <textarea id="product-description" name="description" rows="6"
                      placeholder="Enter detailed product description..." style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; font-family: inherit; resize: vertical;"><?=$edit_product['description'] ?? $_POST['description'] ?? ''?></textarea>
          </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
          <button type="submit" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-save"></i>
            <?=$edit_product ? 'Update Product' : 'Add Product'?>
          </button>
          <a href="products.php" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-times"></i>
            Cancel
          </a>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <h2>Product List</h2>
  <div class="products-overview">
    <div class="stats-cards">
      <div class="stat-card">
        <h3><?=$products ? count($products) : 0?></h3>
        <p>Total Products</p>
      </div>
      <div class="stat-card">
        <h3><?=$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn()?></h3>
        <p>Total Categories</p>
      </div>
      <div class="stat-card">
        <h3><?=$pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn()?></h3>
        <p>Admin Users</p>
      </div>
      <div class="stat-card">
        <h3><?=$pdo->query("SELECT COUNT(*) FROM cart_sessions")->fetchColumn()?></h3>
        <p>Active Carts</p>
      </div>
    </div>

    <div class="products-grid-admin">
      <?php if ($products): ?>
        <?php foreach ($products as $p): ?>
          <div class="product-admin-card">
            <div class="product-admin-image">
              <?php if ($p['image']): ?>
                <img src="../uploads/<?=$p['image']?>" alt="<?=$p['name']?>">
              <?php else: ?>
                <div class="no-image">No Image</div>
              <?php endif; ?>
            </div>
            <div class="product-admin-info">
              <h4><?=$p['name']?></h4>
              <p class="category"><?=$p['category_name']?></p>
              <p class="price">$<?=number_format($p['price'], 2)?></p>
              <div class="product-admin-actions">
                <a href="?edit=<?=$p['id']?>" class="btn btn-small">Edit</a>
                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                  <?php csrf_field(); ?>
                  <input type="hidden" name="product_id" value="<?=$p['id']?>">
                  <button type="submit" name="delete" class="btn btn-small btn-red">Delete</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-products">
          <p>No products found. <a href="?add=1">Add your first product</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>