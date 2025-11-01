<?php
// includes/functions.php
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token() { return $_SESSION['csrf'] ?? ''; }
function csrf_field() { echo '<input type="hidden" name="csrf" value="'.csrf_token().'">'; }
function check_csrf() {
  if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    http_response_code(403);
    die('Invalid CSRF token.');
  }
}

function is_logged_in() { return isset($_SESSION['user']); }
function require_login() {
  if (!is_logged_in()) {
    header('Location: login.php');
    exit;
  }
}

function set_flash($key, $msg) { $_SESSION['flash'][$key] = $msg; }
function get_flash($key) {
  if (isset($_SESSION['flash'][$key])) {
    $m = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $m;
  }
  return null;
}

// Handle a single image upload, return stored filename or throw Exception.
function handle_image_upload($file, $required = false) {
  if (empty($file['name'])) {
    if ($required) throw new Exception('Required image missing.');
    return null;
  }
  if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error.');

  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $file['tmp_name']);
  finfo_close($finfo);

  if (!isset($allowed[$mime])) throw new Exception('Only JPG, PNG, WEBP allowed.');
  if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Max size 5MB.');

  $ext  = $allowed[$mime];
  $name = bin2hex(random_bytes(16)) . "." . $ext;

  $destDir = __DIR__ . '/../uploads/';
  if (!is_dir($destDir)) mkdir($destDir, 0777, true);

  $dest = $destDir . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Failed to save file.');

  return $name;
}
?>