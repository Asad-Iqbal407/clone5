<?php
// admin/users.php - Manage users
require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$success = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();

        if (isset($_POST['delete'])) {
            $user_id = (int)$_POST['user_id'];
            // Prevent admin from deleting themselves
            if ($user_id == $_SESSION['user']['id']) {
                $errors[] = 'You cannot delete your own account.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                set_flash('success', 'User deleted successfully.');
                header('Location: users.php');
                exit;
            }
        }

        if (isset($_POST['toggle_admin'])) {
            $user_id = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 - is_admin WHERE id = ?");
            $stmt->execute([$user_id]);
            set_flash('success', 'User admin status updated successfully.');
            header('Location: users.php');
            exit;
        }

        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $nif = trim($_POST['nif'] ?? '');

        if (!$first_name) $errors[] = 'First name is required.';
        if (!$last_name) $errors[] = 'Last name is required.';
        if (!$email) $errors[] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';

        if (!$errors) {
            if (isset($_POST['user_id'])) {
                // Update
                $user_id = (int)$_POST['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, nif = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $email, $phone, $nif, $user_id]);
                set_flash('success', 'User updated successfully.');
            } else {
                // Add
                $password_hash = password_hash('password123', PASSWORD_DEFAULT); // Default password
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, nif, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $phone, $nif, $password_hash]);
                set_flash('success', 'User added successfully.');
            }
            header('Location: users.php');
            exit;
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

// Fetch users
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

// Get user for editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumbs"><a href="../index.php">Home</a> › <a href="index.php">Admin Panel</a> › Users</div>

<section class="card">
  <h1>Manage Users</h1>

  <?php if ($success): ?>
    <div class="note" style="color:#4caf50"><?=$success?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul class="note" style="color:#e53935"><?php foreach ($errors as $e){ echo "<li>".e($e)."</li>"; } ?></ul>
  <?php endif; ?>

  <a href="?add=1" class="btn">Add New User</a>

  <?php if (isset($_GET['add']) || $edit_user): ?>
    <h2><?=$edit_user ? 'Edit User' : 'Add User'?></h2>
    <form method="post" data-validate>
      <?php csrf_field(); ?>
      <?php if ($edit_user): ?>
        <input type="hidden" name="user_id" value="<?=$edit_user['id']?>">
      <?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" required value="<?=e($edit_user['first_name'] ?? $_POST['first_name'] ?? '')?>">
        </div>
        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" required value="<?=e($edit_user['last_name'] ?? $_POST['last_name'] ?? '')?>">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" required value="<?=e($edit_user['email'] ?? $_POST['email'] ?? '')?>">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="<?=e($edit_user['phone'] ?? $_POST['phone'] ?? '')?>">
        </div>
        <div class="form-group">
          <label>NIF</label>
          <input type="text" name="nif" value="<?=e($edit_user['nif'] ?? $_POST['nif'] ?? '')?>">
        </div>
      </div>
      <div class="form-actions">
        <button class="btn"><?=$edit_user ? 'Update' : 'Add'?> User</button>
        <a href="users.php" class="btn">Cancel</a>
      </div>
    </form>
  <?php endif; ?>

  <h2>User List</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Admin</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?=$u['id']?></td>
          <td><?=$u['first_name']?> <?=$u['last_name']?></td>
          <td><?=$u['email']?></td>
          <td><?=$u['phone']?></td>
          <td><?=$u['is_admin'] ? 'Yes' : 'No'?></td>
          <td><?=$u['created_at']?></td>
          <td>
            <a href="?edit=<?=$u['id']?>" class="btn btn-small">Edit</a>
            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure?')">
              <?php csrf_field(); ?>
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <button type="submit" name="toggle_admin" class="btn btn-small">Toggle Admin</button>
            </form>
            <?php if ($u['id'] != $_SESSION['user']['id']): ?>
              <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                <?php csrf_field(); ?>
                <input type="hidden" name="user_id" value="<?=$u['id']?>">
                <button type="submit" name="delete" class="btn btn-small btn-red">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>