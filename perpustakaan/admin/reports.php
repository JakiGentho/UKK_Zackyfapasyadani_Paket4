<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = 'Laporan';
$month = (int)($_GET['month'] ?? date('m'));
$year  = (int)($_GET['year']  ?? date('Y'));

// ── Statistik Bulan Ini ──
$monthly = $pdo->prepare("
    SELECT
      COUNT(*) AS total,
      SUM(status='returned')         AS returned,
      SUM(status IN ('borrowed','overdue')) AS active,
      SUM(status='overdue')          AS overdue,
      COALESCE(SUM(fine),0)          AS total_fine
    FROM borrowings
    WHERE MONTH(created_at)=? AND YEAR(created_at)=?
");
$monthly->execute([$month, $year]);
$mstat = $monthly->fetch();

// ── Buku Terpopuler ──
$popular = $pdo->query("
    SELECT b.title, b.author,
           COUNT(br.id) AS borrow_count
    FROM   borrowings br
    JOIN   books b ON br.book_id=b.id
    GROUP  BY br.book_id
    ORDER  BY borrow_count DESC
    LIMIT  8
")->fetchAll();

// ── Anggota Teraktif ──
$active_members = $pdo->query("
    SELECT u.name, u.email,
           COUNT(br.id) AS borrow_count,
           SUM(br.fine) AS total_fine
    FROM   borrowings br
    JOIN   users u ON br.user_id=u.id
    WHERE  u.role='user'
    GROUP  BY br.user_id
    ORDER  BY borrow_count DESC
    LIMIT  8
")->fetchAll();

// ── Riwayat per Bulan ──
$monthly_history = $pdo->query("
    SELECT MONTH(created_at) AS m, YEAR(created_at) AS y,
           COUNT(*) AS total
    FROM   borrowings
    GROUP  BY YEAR(created_at), MONTH(created_at)
    ORDER  BY y DESC, m DESC
    LIMIT  12
")->fetchAll();

// ── Daftar Peminjaman Bulan Dipilih ──
$borrow_list = $pdo->prepare("
    SELECT br.*, u.name AS user_name, b.title AS book_title
    FROM   borrowings br
    JOIN   users u ON br.user_id=u.id
    JOIN   books b ON br.book_id=b.id
    WHERE  MONTH(br.created_at)=? AND YEAR(br.created_at)=?
    ORDER  BY br.created_at DESC
");
$borrow_list->execute([$month, $year]);
$borrow_list = $borrow_list->fetchAll();

$month_names = ['','Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'];

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<!-- Filter Bulan/Tahun -->
<div class="content-card mb-4">
  <form method="GET" class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label fw-600">Bulan</label>
      <select name="month" class="form-select">
        <?php for ($m=1; $m<=12; $m++): ?>
          <option value="<?= $m ?>" <?= $m===$month?'selected':'' ?>>
            <?= $month_names[$m] ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-600">Tahun</label>
      <select name="year" class="form-select">
        <?php for ($y=date('Y'); $y>=date('Y')-5; $y--): ?>
          <option value="<?= $y ?>" <?= $y===$year?'selected':'' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">
        <i class="bi bi-search me-1"></i>Tampilkan
      </button>
    </div>
    <div class="col-md-4 text-end">
      <button type="button" class="btn btn-outline-success" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Cetak Laporan
      </button>
    </div>
  </form>
</div>

<!-- Stat Bulan Ini -->
<h6 class="fw-bold mb-3">
  <i class="bi bi-calendar3 me-2 text-primary"></i>
  Statistik <?= $month_names[$month] ?> <?= $year ?>
</h6>
<div class="row g-3 mb-4">
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-primary">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $mstat['total'] ?></div><div class="stat-label">Total Transaksi</div></div>
        <i class="bi bi-arrow-left-right stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-success">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $mstat['returned'] ?></div><div class="stat-label">Dikembalikan</div></div>
        <i class="bi bi-check2-all stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-danger">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $mstat['overdue'] ?></div><div class="stat-label">Terlambat</div></div>
        <i class="bi bi-exclamation-triangle stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-warning">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number" style="font-size:1.3rem">
            <?= formatRupiah($mstat['total_fine']) ?>
          </div>
          <div class="stat-label">Total Denda</div>
        </div>
        <i class="bi bi-cash-coin stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Buku Terpopuler -->
  <div class="col-md-6">
    <div class="content-card h-100">
      <div class="card-header-custom">
        <h6><i class="bi bi-trophy me-2 text-warning"></i>Buku Terpopuler</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover small align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Judul</th><th>Pengarang</th><th>Dipinjam</th></tr>
          </thead>
          <tbody>
            <?php foreach ($popular as $i => $p): ?>
            <tr>
              <td>
                <?php if ($i===0): ?><span class="badge bg-warning text-dark">🥇</span>
                <?php elseif ($i===1): ?><span class="badge bg-secondary">🥈</span>
                <?php elseif ($i===2): ?><span class="badge" style="background:#cd7f32">🥉</span>
                <?php else: ?><?= $i+1 ?><?php endif; ?>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($p['title']) ?></td>
              <td class="text-muted"><?= htmlspecialchars($p['author']) ?></td>
              <td><span class="badge bg-primary"><?= $p['borrow_count'] ?>x</span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Anggota Teraktif -->
  <div class="col-md-6">
    <div class="content-card h-100">
      <div class="card-header-custom">
        <h6><i class="bi bi-person-check me-2 text-success"></i>Anggota Teraktif</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover small align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Nama</th><th>Pinjaman</th><th>Denda</th></tr>
          </thead>
          <tbody>
            <?php foreach ($active_members as $i => $m): ?>
            <tr>
              <td class="text-muted"><?= $i+1 ?></td>
              <td>
                <div class="fw-semibold"><?= htmlspecialchars($m['name']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($m['email']) ?></small>
              </td>
              <td><span class="badge bg-primary"><?= $m['borrow_count'] ?>x</span></td>
              <td class="<?= $m['total_fine']>0?'text-danger':'' ?>">
                <?= $m['total_fine']>0 ? formatRupiah($m['total_fine']) : '-' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Riwayat Bulan Dipilih -->
<div class="content-card">
  <div class="card-header-custom">
    <h6>
      <i class="bi bi-list-ul me-2 text-primary"></i>
      Daftar Peminjaman — <?= $month_names[$month] ?> <?= $year ?>
    </h6>
    <span class="badge bg-secondary"><?= count($borrow_list) ?> transaksi</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover small align-middle">
      <thead class="table-light">
        <tr><th>#</th><th>Anggota</th><th>Buku</th>
            <th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Kembali</th>
            <th>Status</th><th>Denda</th></tr>
      </thead>
      <tbody>
        <?php if ($borrow_list): foreach ($borrow_list as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($r['user_name']) ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($r['book_title']) ?></td>
          <td><?= formatDate($r['borrow_date']) ?></td>
          <td><?= formatDate($r['due_date']) ?></td>
          <td><?= formatDate($r['return_date']) ?></td>
          <td><?= getStatusBadge($r['status']) ?></td>
          <td><?= $r['fine']>0 ? '<span class="text-danger">'.formatRupiah($r['fine']).'</span>' : '-' ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="8">
          <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <p>Tidak ada transaksi pada <?= $month_names[$month] ?> <?= $year ?></p>
          </div>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
