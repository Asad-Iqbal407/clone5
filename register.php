<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    check_csrf();

    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nif   = trim($_POST['nif'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['confirm'] ?? '';

    if ($first === '' || $last === '' || $email === '' || $pass === '') $errors[] = 'Please fill all required fields.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pass !== $conf) $errors[] = 'Passwords do not match.';

    if (!$errors) {
      // unique email
      $st = $pdo->prepare("SELECT id FROM users WHERE email = ?");
      $st->execute([$email]);
      if ($st->fetch()) $errors[] = 'Email already in use.';
    }

    if (!$errors) {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $ins = $pdo->prepare("INSERT INTO users (first_name,last_name,email,phone,nif,password_hash) VALUES (?,?,?,?,?,?)");
      $ins->execute([$first,$last,$email,$phone,$nif,$hash]);

      $id = $pdo->lastInsertId();
      $_SESSION['user'] = [
        'id' => $id,
        'first_name' => $first,
        'last_name' => $last,
        'email' => $email
      ];
      set_flash('ok', 'Account created. Welcome!');
      header('Location: index.php');
      exit;
    }
  } catch (Throwable $e) {
    $errors[] = $e->getMessage();
  }
}

require __DIR__.'/includes/header.php';
?>

<div class="auth-page">
  <div class="auth-container">
    <div class="auth-left">
      <div class="brand-section">
        <div class="brand-logo">
          <span class="logo-icon">📱</span>
          <h1>iMobile</h1>
        </div>
        <h2>Join Our Community!</h2>
        <p>Create your account to access premium mobile devices, expert repair services, and exclusive deals.</p>

        <div class="auth-features">
          <div class="feature-item">
            <div class="feature-icon">🎁</div>
            <span>Exclusive Deals</span>
          </div>
          <div class="feature-item">
            <div class="feature-icon">🚚</div>
            <span>Free Shipping</span>
          </div>
          <div class="feature-item">
            <div class="feature-icon">🛡️</div>
            <span>Premium Support</span>
          </div>
        </div>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form-container">
        <div class="auth-header">
          <h3>Create Account</h3>
          <p>Join thousands of satisfied customers</p>
        </div>

        <?php if ($msg = get_flash('ok')): ?>
          <div class="auth-alert success">
            <div class="alert-icon">✅</div>
            <p><?php echo htmlspecialchars($msg); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($errors): ?>
          <div class="auth-alert error">
            <div class="alert-icon">⚠️</div>
            <ul>
              <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" class="auth-form" data-validate>
          <?php csrf_field(); ?>

          <div class="form-section">
            <h4>Personal Information</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <div class="input-wrapper">
                  <span class="input-icon">👤</span>
                  <input type="text" id="first_name" name="first_name" required
                         placeholder="Enter first name"
                         value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
              </div>

              <div class="form-group">
                <label for="last_name">Last Name</label>
                <div class="input-wrapper">
                  <span class="input-icon">👤</span>
                  <input type="text" id="last_name" name="last_name" required
                         placeholder="Enter last name"
                         value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <div class="input-wrapper">
                <span class="input-icon">📧</span>
                <input type="email" id="email" name="email" required
                       placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                  <span class="input-icon">📞</span>
                  <input type="tel" id="phone" name="phone"
                         placeholder="+1 (555) 123-4567"
                         value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
              </div>

              <div class="form-group">
                <label for="nif">NIF</label>
                <div class="input-wrapper">
                  <span class="input-icon">🆔</span>
                  <input type="text" id="nif" name="nif"
                         placeholder="Enter NIF"
                         value="<?php echo htmlspecialchars($_POST['nif'] ?? ''); ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4>Security</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔒</span>
                  <input type="password" id="password" name="password" required
                         placeholder="Create password">
                </div>
              </div>

              <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔐</span>
                  <input type="password" id="confirm" name="confirm" required
                         placeholder="Confirm password">
                </div>
              </div>
            </div>
          </div>

          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox" name="terms" required>
              <span class="checkmark"></span>
              I agree to the <a href="#" class="terms-link">Terms & Conditions</a>
            </label>
          </div>

          <button type="submit" class="auth-btn primary">
            <span>Create Account</span>
            <div class="btn-loader" style="display: none;"></div>
          </button>
        </form>

        <div class="auth-divider">
          <span>or</span>
        </div>

        <div class="auth-footer">
          <p>Already have an account?
            <a href="login.php" class="auth-link">Sign In</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.auth-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.auth-container {
  display: flex;
  max-width: 1200px;
  width: 100%;
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  overflow: hidden;
  min-height: 700px;
}

.auth-left {
  flex: 1;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  padding: 60px 50px;
  display: flex;
  align-items: center;
  position: relative;
}

.auth-left::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="register-bg" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23register-bg)"/></svg>');
  opacity: 0.1;
}

.brand-section {
  color: white;
  position: relative;
  z-index: 2;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 30px;
}

.logo-icon {
  font-size: 3rem;
  background: rgba(255,255,255,0.2);
  padding: 15px;
  border-radius: 16px;
  backdrop-filter: blur(10px);
}

.brand-logo h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0;
  background: linear-gradient(45deg, #fff, #e2e8f0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.auth-left h2 {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 20px;
  line-height: 1.2;
}

.auth-left p {
  font-size: 1.1rem;
  opacity: 0.9;
  line-height: 1.6;
  margin-bottom: 40px;
}

.auth-features {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px 0;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.feature-item:last-child {
  border-bottom: none;
}

.feature-icon {
  font-size: 1.5rem;
  background: rgba(255,255,255,0.2);
  padding: 10px;
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.feature-item span {
  font-weight: 600;
  font-size: 1rem;
}

.auth-right {
  flex: 1;
  padding: 60px 50px;
  display: flex;
  align-items: center;
  background: #fafbfc;
  overflow-y: auto;
  max-height: 700px;
}

.auth-form-container {
  width: 100%;
  max-width: 500px;
}

.auth-header {
  text-align: center;
  margin-bottom: 40px;
}

.auth-header h3 {
  font-size: 2rem;
  font-weight: 800;
  color: #1a202c;
  margin: 0 0 10px;
}

.auth-header p {
  color: #718096;
  font-size: 1rem;
  margin: 0;
}

.auth-alert {
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 30px;
  display: flex;
  align-items: flex-start;
  gap: 15px;
}

.auth-alert.success {
  background: #c6f6d5;
  border: 1px solid #68d391;
  color: #22543d;
}

.auth-alert.error {
  background: #fed7d7;
  border: 1px solid #fc8181;
  color: #c53030;
}

.alert-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.auth-alert ul {
  margin: 0;
  padding: 0;
  list-style: none;
}

.auth-alert li {
  margin-bottom: 5px;
}

.auth-alert p {
  margin: 0;
}

.auth-form {
  margin-bottom: 30px;
}

.form-section {
  margin-bottom: 30px;
}

.form-section h4 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e2e8f0;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 8px;
  font-size: 0.95rem;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
  color: #a0aec0;
  z-index: 1;
}

.form-group input {
  width: 100%;
  padding: 16px 16px 16px 50px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  transform: translateY(-1px);
}

.form-options {
  margin-bottom: 30px;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-size: 0.9rem;
  color: #4a5568;
  cursor: pointer;
  line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
  display: none;
  margin-top: 2px;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #cbd5e0;
  border-radius: 4px;
  position: relative;
  transition: all 0.3s ease;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-label input:checked + .checkmark {
  background: #667eea;
  border-color: #667eea;
}

.checkbox-label input:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: -2px;
  left: 2px;
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.terms-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.terms-link:hover {
  text-decoration: underline;
}

.auth-btn {
  width: 100%;
  padding: 16px 24px;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  position: relative;
  overflow: hidden;
}

.auth-btn.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.auth-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.auth-btn.primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.auth-btn.primary:hover::before {
  left: 100%;
}

.btn-loader {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.auth-divider {
  text-align: center;
  margin: 30px 0;
  position: relative;
}

.auth-divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e2e8f0;
}

.auth-divider span {
  background: #fafbfc;
  padding: 0 20px;
  color: #718096;
  font-size: 0.9rem;
  position: relative;
  z-index: 1;
}

.auth-footer {
  text-align: center;
}

.auth-footer p {
  color: #718096;
  margin: 0;
  font-size: 0.95rem;
}

.auth-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.auth-link:hover {
  color: #5a67d8;
  text-decoration: underline;
}

@media (max-width: 768px) {
  .auth-container {
    flex-direction: column;
    min-height: auto;
  }

  .auth-left {
    padding: 40px 30px;
    text-align: center;
  }

  .auth-right {
    padding: 40px 30px;
    max-height: none;
    overflow-y: visible;
  }

  .brand-logo h1 {
    font-size: 2rem;
  }

  .auth-left h2 {
    font-size: 1.8rem;
  }

  .auth-features {
    max-width: 300px;
    margin: 0 auto;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .auth-form-container {
    max-width: none;
  }
}
</style>

<?php include __DIR__.'/includes/footer.php'; ?>
