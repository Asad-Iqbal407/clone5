<?php
// admin/login.php - Admin login page
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
        if (!$errors) {
            $st = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1");
            $st->execute([$email]);
            $u = $st->fetch();
            if (!$u || !password_verify($pass, $u['password_hash'])) {
                $errors[] = 'Wrong email or password.';
            } else {
                $_SESSION['user'] = [
                    'id'=>$u['id'],'first_name'=>$u['first_name'],'last_name'=>$u['last_name'],'email'=>$u['email'],'is_admin'=>$u['is_admin']
                ];
                header('Location: index.php');
                exit;
            }
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="breadcrumbs"><a href="../index.php">Home</a> › Admin Login</div>

<section class="card">
  <h1>Admin Login</h1>
  <?php if ($errors): ?>
    <ul class="note" style="color:#e53935"><?php foreach ($errors as $e){ echo "<li>".e($e)."</li>"; } ?></ul>
  <?php endif; ?>
  <form method="post" data-validate>
    <?php csrf_field(); ?>
    <div class="form-grid">
      <div class="form-group">
        <label>E‑Mail *</label>
        <input type="email" name="email" required placeholder="admin@imobile.com" value="<?=e($_POST['email'] ?? '')?>">
      </div>
      <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" required placeholder="Your password">
      </div>
    </div>
    <div class="form-actions">
      <button class="btn">Login</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>