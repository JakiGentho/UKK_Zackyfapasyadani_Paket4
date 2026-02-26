<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireUser();
updateOverdue($pdo);

$page_title = 'Beranda';
$uid        = $_SESSION['user_id'];

$stats = [
    'active'   => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status IN ('borrowed','overdue')"),
    'pending'  => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status='pending'"),
    'returned' => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status='returned'"),
    'overdue'  => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status='overdue'"),
];
foreach ($stats as $key => $s) { $s->execute([$uid]); $stats[$key] = (int)$s->fetchColumn(); }

$active_borrows = $pdo->prepare("
    SELECT br.*, b.title AS book_title, b.author AS book_author, b.cover AS book_cover
    FROM   borrowings br
    JOIN   books b ON br.book_id=b.id
    WHERE  br.user_id=? AND br.status IN ('borrowed','overdue')
    ORDER  BY br.due_date ASC
");
$active_borrows->execute([$uid]);
$active_borrows = $active_borrows->fetchAll();

$pending_borrows = $pdo->prepare("
    SELECT br.*, b.title AS book_title, b.author AS book_author
    FROM   borrowings br
    JOIN   books b ON br.book_id=b.id
    WHERE  br.user_id=? AND br.status='pending'
    ORDER  BY br.created_at DESC
");
$pending_borrows->execute([$uid]);
$pending_borrows = $pending_borrows->fetchAll();

include '../includes/user_navbar.php';
?>

<?php showFlash(); ?>

<!-- Sapaan -->
<div class="p-4 mb-4 rounded-3 text-white"
     style="background:linear-gradient(135deg,#667eea,#764ba2);">
  <h5 class="fw-bold mb-1">
    <i class="bi bi-hand-wave me-2"></i>
    Halo, <?= htmlspecialchars($_SESSION['name']) ?>!
  </h5>
  <p class="mb-0 opacity-75 small">Selamat datang di Sistem Perpustakaan Digital</p>
</div>

<!-- Stat -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-box stat-primary">
      <span class="stat-icon"><i class="bi bi-book-half"></i></span>
      <div class="stat-number"><?= $stats['active'] ?></div>
      <div class="stat-label">Sedang Dipinjam</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-warning">
      <span class="stat-icon"><i class="bi bi-hourglass-split"></i></span>
      <div class="stat-number"><?= $stats['pending'] ?></div>
      <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-success">
      <span class="stat-icon"><i class="bi bi-check2-all"></i></span>
      <div class="stat-number"><?= $stats['returned'] ?></div>
      <div class="stat-label">Sudah Dikembalikan</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-danger">
      <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
      <div class="stat-number"><?= $stats['overdue'] ?></div>
      <div class="stat-label">Terlambat</div>
    </div>
  </div>
</div>

<!-- Alert overdue -->
<?php if ($stats['overdue']): ?>
<div class="alert alert-danger d-flex align-items-center gap-3 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-4"></i>
  <div>
    <strong>Anda memiliki <?= $stats['overdue'] ?> buku terlambat dikembalikan!</strong>
    Segera kembalikan untuk menghindari denda tambahan.
    <a href="<?= BASE_URL ?>user/history.php" class="alert-link ms-2">Lihat →</a>
  </div>
</div>
<?php endif; ?>

<!-- Buku Aktif -->
<?php if ($active_borrows): ?>
<div class="user-card mb-4">
  <div class="user-card-header">
    <h6 class="user-card-title">
      <i class="bi bi-book me-2 text-primary"></i>Buku yang Sedang Dipinjam
    </h6>
    <a href="<?= BASE_URL ?>user/history.php" class="btn btn-outline-primary btn-sm">
      Lihat Riwayat
    </a>
  </div>
  <div class="row g-3">
    <?php foreach ($active_borrows as $b): ?>
    <div class="col-md-6">
      <div class="d-flex gap-3 p-3 rounded-3 border bg-light">
        <?php if ($b['book_cover']): ?>
          <img src="<?= BASE_URL ?>uploads/covers/<?= $b['book_cover'] ?>"
               width="50" height="65" style="object-fit:cover;border-radius:6px;flex-shrink:0;">
        <?php else: ?>
          <div style="width:50px;height:65px;background:linear-gradient(135deg,#667eea,#764ba2);
                      border-radius:6px;display:flex;align-items:center;justify-content:center;
                      color:rgba(255,255,255,.6);flex-shrink:0;">
            <i class="bi bi-book"></i>
          </div>
        <?php endif; ?>
        <div class="flex-fill min-w-0">
          <div class="fw-semibold small"><?= htmlspecialchars($b['book_title']) ?></div>
          <div class="text-muted" style="font-size:.8rem"><?= htmlspecialchars($b['book_author']) ?></div>
          <div class="mt-1">
            <?= getStatusBadge($b['status']) ?>
          </div>
          <div class="mt-1">
            <span class="small text-muted">Jatuh tempo:</span>
            <span data-due-date="<?= $b['due_date'] ?>" class="small fw-semibold">
              <?= formatDate($b['due_date']) ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Menunggu Konfirmasi -->
<?php if ($pending_borrows): ?>
<div class="user-card mb-4">
  <div class="user-card-header">
    <h6 class="user-card-title">
      <i class="bi bi-hourglass-split me-2 text-warning"></i>
      Menunggu Konfirmasi Admin
    </h6>
  </div>
  <?php foreach ($pending_borrows as $p): ?>
  <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border mb-2">
    <div>
      <div class="fw-semibold"><?= htmlspecialchars($p['book_title']) ?></div>
      <small class="text-muted">
        Diajukan: <?= formatDate($p['created_at']) ?>
      </small>
    </div>
    <?= getStatusBadge($p['status']) ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- CTA -->
<?php if (!$stats['active'] && !$stats['pending']): ?>
<div class="text-center py-4">
  <i class="bi bi-book-half display-1 text-muted opacity-25 d-block mb-3"></i>
  <h5 class="text-muted">Belum ada peminjaman aktif</h5>
  <p class="text-muted">Mulai pinjam buku dari katalog kami</p>
  <a href="<?= BASE_URL ?>user/books.php" class="btn btn-gradient btn-lg mt-2">
    <i class="bi bi-search me-2"></i>Lihat Katalog Buku
  </a>
</div>
<?php endif; ?>

<?php include '../includes/user_footer.php'; ?>
