<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: '.BASE_URL.(isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ? AND status = 'active'"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            header('Location: '.BASE_URL.
                ($user['role']==='admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit;
        }
        $error = 'Email / password salah atau akun tidak aktif!';
    } else {
        $error = 'Email dan password wajib diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Perpustakaan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/auth.css">
</head>
<body class="auth-body">

<div class="auth-card">
  <div class="auth-header">
    <i class="auth-icon bi bi-book-half"></i>
    <h4>Perpustakaan Digital</h4>
    <small>Masuk untuk mengakses sistem</small>
  </div>
  <div class="auth-body">
    <?php if ($error): ?>
      <div class="alert alert-danger auto-dismiss d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label" for="emailInput">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
          <input type="email" id="emailInput" name="email" class="form-control"
                 placeholder="contoh@email.com"
                 value="<?= sanitize($_POST['email'] ?? '') ?>" required autofocus>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label" for="passwordInput">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
          <input type="password" id="passwordInput" name="password"
                 class="form-control" placeholder="Masukkan password" required>
          <button class="btn btn-outline-secondary toggle-password"
                  type="button" data-target="passwordInput">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-auth btn">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>

    <div class="auth-divider"><span>Info</span></div>
    <div class="auth-info-box">
      <i class="bi bi-info-circle me-2 text-primary"></i>
      Belum punya akun? Hubungi <strong>Administrator</strong>
      untuk mendaftarkan akun Anda.
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
<script src="<?= BASE_URL ?>assets/js/auth.js"></script>
</body>
</html>
