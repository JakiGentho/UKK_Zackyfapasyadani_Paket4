<?php
$current = basename($_SERVER['PHP_SELF']);
global $pdo;
$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM borrowings
     WHERE user_id = ? AND status IN ('borrowed','overdue')"
);
$stmt->execute([$_SESSION['user_id']]);
$activeCount = (int)$stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title ?? 'Perpustakaan') ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/user.css">
</head>
<body class="user-body">

<nav class="navbar navbar-expand-lg user-navbar">
  <div class="container">
    <a class="navbar-brand text-white fw-bold"
       href="<?= BASE_URL ?>user/dashboard.php">
      <i class="bi bi-book-half me-2 text-warning"></i>Perpustakaan
    </a>
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#userNav">
      <i class="bi bi-list fs-3 text-white"></i>
    </button>
    <div class="collapse navbar-collapse" id="userNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= $current==='dashboard.php'?'active':'' ?>"
             href="<?= BASE_URL ?>user/dashboard.php">
            <i class="bi bi-house me-1"></i>Beranda
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='books.php'?'active':'' ?>"
             href="<?= BASE_URL ?>user/books.php">
            <i class="bi bi-book me-1"></i>Katalog Buku
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='history.php'?'active':'' ?>"
             href="<?= BASE_URL ?>user/history.php">
            <i class="bi bi-clock-history me-1"></i>Riwayat
            <?php if ($activeCount): ?>
              <span class="badge bg-warning text-dark"><?= $activeCount ?></span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['name']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li>
              <a class="dropdown-item" href="<?= BASE_URL ?>user/profile.php">
                <i class="bi bi-person me-2 text-primary"></i>Profil Saya
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php"
                 data-confirm="Yakin ingin logout?">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="user-main">
  <div class="container">
