<?php
// admin/categories.php - Manage categories
require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$success = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();

        if (isset($_POST['delete'])) {
            $category_id = (int)$_POST['category_id'];
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            set_flash('success', 'Category deleted successfully.');
            header('Location: categories.php');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (!$name) $errors[] = 'Category name is required.';

        if (!$errors) {
            if (isset($_POST['category_id'])) {
                // Update
                $category_id = (int)$_POST['category_id'];
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ?, parent_id = ? WHERE id = ?");
                $stmt->execute([$name, $icon, $parent_id, $category_id]);
                set_flash('success', 'Category updated successfully.');
            } else {
                // Add
                $stmt = $pdo->prepare("INSERT INTO categories (name, icon, parent_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $icon, $parent_id]);
                set_flash('success', 'Category added successfully.');
            }
            header('Location: categories.php');
            exit;
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

// Fetch categories with hierarchy
$categories = $pdo->query("
    SELECT
        c.*,
        p.name as parent_name,
        (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as subcategories_count,
        (SELECT COUNT(*) FROM products WHERE category_id = c.id) as products_count
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NULL DESC, c.name
")->fetchAll();

// Get category for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_category = $stmt->fetch();
}

// Get parent categories for dropdown
$parent_categories = $pdo->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumbs"><a href="../index.php">Home</a> › <a href="index.php">Admin Panel</a> › Categories</div>

<section class="card">
  <h1>Manage Categories</h1>

  <?php if ($success): ?>
    <div class="note" style="color:#4caf50"><?=$success?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul class="note" style="color:#e53935"><?php foreach ($errors as $e){ echo "<li>".e($e)."</li>"; } ?></ul>
  <?php endif; ?>

  <a href="?add=1" class="btn">Add New Category</a>

  <?php if (isset($_GET['add']) || $edit_category): ?>
    <h2><?=$edit_category ? 'Edit Category' : 'Add Category'?></h2>
    <form method="post" data-validate>
      <?php csrf_field(); ?>
      <?php if ($edit_category): ?>
        <input type="hidden" name="category_id" value="<?=$edit_category['id']?>">
      <?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label>Name *</label>
          <input type="text" name="name" required value="<?=e($edit_category['name'] ?? $_POST['name'] ?? '')?>">
        </div>
        <div class="form-group">
          <label>Icon Class (Font Awesome)</label>
          <input type="text" name="icon" placeholder="fa-mobile-screen" value="<?=e($edit_category['icon'] ?? $_POST['icon'] ?? '')?>">
          <small>Optional Font Awesome icon class (e.g., fa-mobile-screen)</small>
        </div>
        <div class="form-group">
          <label>Parent Category</label>
          <select name="parent_id">
            <option value="">-- Main Category --</option>
            <?php foreach ($parent_categories as $parent): ?>
              <option value="<?=$parent['id']?>" <?=(($edit_category['parent_id'] ?? null) == $parent['id']) ? 'selected' : ''?>><?=$parent['name']?></option>
            <?php endforeach; ?>
          </select>
          <small>Leave empty for main category, or select a parent for subcategory</small>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn"><?=$edit_category ? 'Update' : 'Add'?> Category</button>
        <a href="categories.php" class="btn">Cancel</a>
      </div>
    </form>
  <?php endif; ?>

  <h2>Categories</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Icon</th>
        <th>Type</th>
        <th>Parent</th>
        <th>Products</th>
        <th>Subcategories</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?=$cat['id']?></td>
          <td><?=$cat['name']?></td>
          <td><?php if ($cat['icon']): ?><i class="<?=$cat['icon']?>"></i> <?=$cat['icon']?><?php endif; ?></td>
          <td><?=$cat['parent_id'] ? 'Subcategory' : 'Main Category'?></td>
          <td><?=$cat['parent_name'] ?: 'N/A'?></td>
          <td><?=$cat['products_count']?></td>
          <td><?=$cat['subcategories_count']?></td>
          <td>
            <a href="?edit=<?=$cat['id']?>" class="btn btn-small">Edit</a>
            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure? This will also delete all subcategories and may affect products.')">
              <?php csrf_field(); ?>
              <input type="hidden" name="category_id" value="<?=$cat['id']?>">
              <button type="submit" name="delete" class="btn btn-small btn-red">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>