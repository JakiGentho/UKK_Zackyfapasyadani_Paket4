<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Halaman aktif
$current = basename($_SERVER['PHP_SELF']);

// Badge: permintaan pending
$pendingStmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE status = 'pending'");
$pendingCount = (int)$pendingStmt->fetchColumn();

// Badge: terlambat
$overdueStmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE status = 'overdue'");
$overdueCount = (int)$overdueStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Admin') ?> — Perpustakaan</title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Admin CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="admin-body">

<!-- ── Overlay Mobile ── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ======================================================
     SIDEBAR
====================================================== -->
<aside class="sidebar" id="sidebar">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-icon">
      <i class="bi bi-book-half"></i>
    </div>
    <div class="brand-text">
      <h5>Perpustakaan</h5>
      <small>Admin Panel</small>
    </div>
  </div>

  <!-- Navigation -->
  <nav>

    <!-- Menu Utama -->
    <div class="nav-section">Menu Utama</div>

    <a href="<?= BASE_URL ?>admin/dashboard.php"
       class="nav-link <?= in_array($current, ['dashboard.php']) ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span class="nav-text">Dashboard</span>
    </a>

    <!-- Kelola Data -->
    <div class="nav-section">Kelola Data</div>

    <a href="<?= BASE_URL ?>admin/books.php"
       class="nav-link <?= in_array($current, ['books.php','books_add.php','books_edit.php']) ? 'active' : '' ?>">
      <i class="bi bi-book"></i>
      <span class="nav-text">Buku</span>
    </a>

    <a href="<?= BASE_URL ?>admin/categories.php"
       class="nav-link <?= $current === 'categories.php' ? 'active' : '' ?>">
      <i class="bi bi-tags"></i>
      <span class="nav-text">Kategori</span>
    </a>

    <a href="<?= BASE_URL ?>admin/users.php"
       class="nav-link <?= in_array($current, ['users.php','users_edit.php']) ? 'active' : '' ?>">
      <i class="bi bi-people"></i>
      <span class="nav-text">Pengguna</span>
    </a>

    <!-- Transaksi -->
    <div class="nav-section">Transaksi</div>

    <a href="<?= BASE_URL ?>admin/borrowings.php"
       class="nav-link <?= $current === 'borrowings.php' ? 'active' : '' ?>">
      <i class="bi bi-arrow-left-right"></i>
      <span class="nav-text">Peminjaman</span>
      <?php if ($pendingCount > 0): ?>
        <span class="badge-count"><?= $pendingCount ?></span>
      <?php endif; ?>
    </a>

    <a href="<?= BASE_URL ?>admin/reports.php"
       class="nav-link <?= $current === 'reports.php' ? 'active' : '' ?>">
      <i class="bi bi-bar-chart-line"></i>
      <span class="nav-text">Laporan</span>
    </a>

    <!-- Akun -->
    <div class="nav-section">Akun</div>

    <a href="<?= BASE_URL ?>logout.php"
       class="nav-link nav-logout"
       data-confirm="Yakin ingin logout?">
      <i class="bi bi-box-arrow-right"></i>
      <span class="nav-text">Logout</span>
    </a>

  </nav>
</aside>

<!-- ======================================================
     MAIN CONTENT
====================================================== -->
<div class="main-content">

  <!-- ── Topbar ── -->
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <!-- Toggle button (mobile) -->
      <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
        <i class="bi bi-list" style="font-size:1.25rem;"></i>
      </button>
      <!-- Breadcrumb / Page Title -->
      <div>
        <h6 class="topbar-title mb-0">
          <?= htmlspecialchars($page_title ?? 'Dashboard') ?>
        </h6>
        <nav aria-label="breadcrumb" class="d-none d-md-block">
          <ol class="breadcrumb mb-0" style="font-size:.75rem;">
            <li class="breadcrumb-item">
              <a href="<?= BASE_URL ?>admin/dashboard.php"
                 class="text-decoration-none" style="color:var(--primary);">
                Admin
              </a>
            </li>
            <li class="breadcrumb-item active text-muted">
              <?= htmlspecialchars($page_title ?? 'Dashboard') ?>
            </li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Topbar Right -->
    <div class="topbar-right">
      <!-- Alert overdue -->
      <?php if ($overdueCount > 0): ?>
        <a href="<?= BASE_URL ?>admin/borrowings.php?status=overdue"
           class="btn btn-sm btn-danger d-none d-sm-inline-flex"
           data-bs-toggle="tooltip"
           title="<?= $overdueCount ?> peminjaman terlambat">
          <i class="bi bi-exclamation-triangle-fill me-1"></i>
          <?= $overdueCount ?> Terlambat
        </a>
      <?php endif; ?>

      <!-- Alert pending -->
      <?php if ($pendingCount > 0): ?>
        <a href="<?= BASE_URL ?>admin/borrowings.php?status=pending"
           class="btn btn-sm btn-warning text-dark d-none d-sm-inline-flex"
           data-bs-toggle="tooltip"
           title="<?= $pendingCount ?> permintaan menunggu">
          <i class="bi bi-hourglass-split me-1"></i>
          <?= $pendingCount ?> Pending
        </a>
      <?php endif; ?>

      <!-- User Info -->
      <div class="topbar-user d-none d-md-flex">
        <i class="bi bi-shield-check"></i>
        <span><?= htmlspecialchars($_SESSION['name']) ?></span>
      </div>

      <!-- Logout -->
      <a href="<?= BASE_URL ?>logout.php"
         class="btn btn-sm btn-outline-danger"
         data-confirm="Yakin ingin logout?"
         data-bs-toggle="tooltip"
         title="Logout">
        <i class="bi bi-box-arrow-right"></i>
        <span class="d-none d-sm-inline ms-1">Logout</span>
      </a>
    </div>
  </div>
  <!-- /.topbar -->

  <!-- ── Page Container ── -->
  <div class="page-container">
