<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();
updateOverdue($pdo);

$page_title = 'Dashboard';

$stats = [
    'books'    => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'users'    => $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
    'borrowed' => $pdo->query("SELECT COUNT(*) FROM borrowings WHERE status IN ('borrowed','overdue')")->fetchColumn(),
    'pending'  => $pdo->query("SELECT COUNT(*) FROM borrowings WHERE status='pending'")->fetchColumn(),
    'overdue'  => $pdo->query("SELECT COUNT(*) FROM borrowings WHERE status='overdue'")->fetchColumn(),
    'fine'     => $pdo->query("SELECT COALESCE(SUM(fine),0) FROM borrowings WHERE status='returned'")->fetchColumn(),
];

$recent = $pdo->query("
    SELECT br.*, u.name AS user_name, b.title AS book_title
    FROM   borrowings br
    JOIN   users u ON br.user_id = u.id
    JOIN   books b ON br.book_id = b.id
    ORDER  BY br.created_at DESC LIMIT 8
")->fetchAll();

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card stat-card text-white bg-gradient-primary">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $stats['books'] ?></div><div class="stat-label">Total Buku</div></div>
        <i class="bi bi-book stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card stat-card text-white bg-gradient-info">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $stats['users'] ?></div><div class="stat-label">Anggota</div></div>
        <i class="bi bi-people stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card stat-card text-white bg-gradient-success">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $stats['borrowed'] ?></div><div class="stat-label">Sedang Dipinjam</div></div>
        <i class="bi bi-arrow-left-right stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card stat-card text-white bg-gradient-danger">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $stats['pending'] ?></div><div class="stat-label">Permintaan Baru</div></div>
        <i class="bi bi-hourglass-split stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<?php if ($stats['overdue']): ?>
<div class="alert alert-danger d-flex align-items-center gap-3 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-4"></i>
  <div>
    <strong><?= $stats['overdue'] ?> peminjaman terlambat!</strong>
    <a href="<?= BASE_URL ?>admin/borrowings.php?status=overdue" class="alert-link ms-2">Lihat →</a>
  </div>
</div>
<?php endif; ?>

<?php if ($stats['fine'] > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
  <i class="bi bi-cash-coin fs-4"></i>
  <div>Total denda terkumpul: <strong><?= formatRupiah($stats['fine']) ?></strong></div>
</div>
<?php endif; ?>

<div class="content-card">
  <div class="card-header-custom">
    <h6><i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Terbaru</h6>
    <a href="<?= BASE_URL ?>admin/borrowings.php" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th><th>Anggota</th><th>Buku</th>
          <th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($recent): foreach ($recent as $i => $r): ?>
        <tr>
          <td class="text-muted"><?= $i+1 ?></td>
          <td><?= htmlspecialchars($r['user_name']) ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($r['book_title']) ?></td>
          <td><?= formatDate($r['borrow_date']) ?></td>
          <td>
            <?php if ($r['due_date']): ?>
              <span data-due-date="<?= $r['due_date'] ?>">
                <?= formatDate($r['due_date']) ?>
              </span>
            <?php else: ?> - <?php endif; ?>
          </td>
          <td><?= getStatusBadge($r['status']) ?></td>
          <td>
            <a href="<?= BASE_URL ?>admin/borrowings.php?detail=<?= $r['id'] ?>"
               class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Detail">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <i class="bi bi-inbox"></i><p>Belum ada aktivitas peminjaman</p>
            </div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
