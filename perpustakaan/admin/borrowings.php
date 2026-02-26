<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();
updateOverdue($pdo);

$page_title  = 'Kelola Peminjaman';
$status_filter = $_GET['status'] ?? '';
$search        = sanitize($_GET['search'] ?? '');
$page          = max(1,(int)($_GET['page'] ?? 1));
$per_page      = 12;

// ── Approve ───────────────────────────────────────────────
if (isset($_GET['approve'])) {
    $bid   = (int)$_GET['approve'];
    $borrow = $pdo->prepare("SELECT * FROM borrowings WHERE id=? AND status='pending'");
    $borrow->execute([$bid]);
    $row   = $borrow->fetch();
    if ($row) {
        $due = date('Y-m-d', strtotime('+'.LOAN_DAYS.' days'));
        $pdo->prepare(
            "UPDATE borrowings SET status='borrowed', borrow_date=CURDATE(), due_date=? WHERE id=?"
        )->execute([$due, $bid]);
        $pdo->prepare(
            "UPDATE books SET available_stock = available_stock-1 WHERE id=? AND available_stock>0"
        )->execute([$row['book_id']]);
        setFlash('success','Peminjaman berhasil disetujui!');
    }
    header('Location: borrowings.php'); exit;
}

// ── Reject ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reject'])) {
    $bid   = (int)$_POST['bid'];
    $notes = sanitize($_POST['admin_notes'] ?? '');
    $pdo->prepare(
        "UPDATE borrowings SET status='rejected', admin_notes=? WHERE id=? AND status='pending'"
    )->execute([$notes, $bid]);
    setFlash('warning','Peminjaman ditolak.');
    header('Location: borrowings.php'); exit;
}

// ── Return ─────────────────────────────────────────────────
if (isset($_GET['return'])) {
    $bid  = (int)$_GET['return'];
    $stmt = $pdo->prepare(
        "SELECT * FROM borrowings WHERE id=? AND status IN ('borrowed','overdue')"
    );
    $stmt->execute([$bid]);
    $row  = $stmt->fetch();
    if ($row) {
        $days_late = getDaysLate($row['due_date']);
        $fine      = calculateFine($days_late);
        $pdo->prepare(
            "UPDATE borrowings SET status='returned', return_date=CURDATE(), fine=? WHERE id=?"
        )->execute([$fine, $bid]);
        $pdo->prepare(
            "UPDATE books SET available_stock = available_stock+1 WHERE id=?"
        )->execute([$row['book_id']]);
        $msg = "Buku berhasil dikembalikan.";
        if ($fine > 0) $msg .= " Denda: <strong>".formatRupiah($fine)."</strong>";
        setFlash('success', $msg);
    }
    header('Location: borrowings.php'); exit;
}

// ── Query ──────────────────────────────────────────────────
$where  = '1=1';
$params = [];
if ($status_filter && in_array($status_filter,
    ['pending','approved','rejected','borrowed','returned','overdue'],true)) {
    $where   .= " AND br.status=?";
    $params[] = $status_filter;
}
if ($search) {
    $where   .= " AND (u.name LIKE ? OR b.title LIKE ?)";
    $params   = array_merge($params,["%$search%","%$search%"]);
}

$total_rows  = $pdo->prepare("
    SELECT COUNT(*) FROM borrowings br
    JOIN users u ON br.user_id=u.id
    JOIN books b ON br.book_id=b.id
    WHERE $where
");
$total_rows->execute($params);
$total_rows  = (int)$total_rows->fetchColumn();
$total_pages = max(1, (int)ceil($total_rows / $per_page));
$offset      = ($page-1) * $per_page;

$stmt = $pdo->prepare("
    SELECT br.*, u.name AS user_name, u.email AS user_email,
           b.title AS book_title, b.author AS book_author
    FROM   borrowings br
    JOIN   users u ON br.user_id = u.id
    JOIN   books b ON br.book_id = b.id
    WHERE  $where
    ORDER  BY FIELD(br.status,'pending','overdue','borrowed','approved','returned','rejected'),
              br.created_at DESC
    LIMIT  $per_page OFFSET $offset
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Count per status
$counts = [];
foreach (['pending','approved','borrowed','returned','rejected','overdue'] as $s) {
    $c = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE status=?");
    $c->execute([$s]);
    $counts[$s] = $c->fetchColumn();
}

// Detail modal
$detail = null;
if (isset($_GET['detail'])) {
    $ds = $pdo->prepare("
        SELECT br.*, u.name AS user_name, u.email AS user_email,
               u.phone AS user_phone,
               b.title AS book_title, b.author AS book_author, b.isbn AS book_isbn
        FROM   borrowings br
        JOIN   users u ON br.user_id=u.id
        JOIN   books b ON br.book_id=b.id
        WHERE  br.id=?
    ");
    $ds->execute([(int)$_GET['detail']]);
    $detail = $ds->fetch();
}

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<!-- Filter Tabs -->
<div class="content-card mb-3 py-2">
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <a href="borrowings.php" class="btn btn-sm <?= !$status_filter?'btn-dark':'btn-outline-secondary' ?>">
      Semua <span class="badge bg-secondary ms-1"><?= array_sum($counts) ?></span>
    </a>
    <?php
    $tab_colors = [
      'pending'  => 'warning',
      'borrowed' => 'primary',
      'overdue'  => 'danger',
      'returned' => 'success',
      'rejected' => 'secondary',
    ];
    $tab_labels = [
      'pending'  => 'Menunggu',
      'borrowed' => 'Dipinjam',
      'overdue'  => 'Terlambat',
      'returned' => 'Dikembalikan',
      'rejected' => 'Ditolak',
    ];
    foreach ($tab_colors as $s => $c):
    ?>
      <a href="?status=<?= $s ?>"
         class="btn btn-sm <?= $status_filter===$s?"btn-$c":"btn-outline-$c" ?>">
        <?= $tab_labels[$s] ?>
        <span class="badge bg-white text-dark ms-1"><?= $counts[$s] ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="content-card">
  <div class="card-header-custom">
    <h6><i class="bi bi-arrow-left-right me-2 text-primary"></i>Data Peminjaman</h6>
    <!-- Search -->
    <form method="GET" class="d-flex gap-2">
      <?php if ($status_filter): ?>
        <input type="hidden" name="status" value="<?= $status_filter ?>">
      <?php endif; ?>
      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="Cari nama / judul..."
             value="<?= htmlspecialchars($search) ?>" style="width:200px;">
      <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle small">
      <thead class="table-light">
        <tr>
          <th>#</th><th>Anggota</th><th>Buku</th>
          <th>Tgl Pinjam</th><th>Jatuh Tempo</th>
          <th>Status</th><th>Denda</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows): foreach ($rows as $i => $r): ?>
        <tr>
          <td class="text-muted"><?= ($offset+$i+1) ?></td>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['user_name']) ?></div>
            <small class="text-muted"><?= htmlspecialchars($r['user_email']) ?></small>
          </td>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['book_title']) ?></div>
            <small class="text-muted"><?= htmlspecialchars($r['book_author']) ?></small>
          </td>
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
            <?= $r['fine']>0
              ? '<span class="text-danger fw-semibold">'.formatRupiah($r['fine']).'</span>'
              : '-' ?>
          </td>
          <td>
            <div class="d-flex gap-1 flex-wrap">
              <a href="?detail=<?= $r['id'] ?>" class="btn btn-sm btn-outline-info">
                <i class="bi bi-eye"></i>
              </a>
              <?php if ($r['status']==='pending'): ?>
                <a href="?approve=<?= $r['id'] ?>"
                   class="btn btn-sm btn-outline-success btn-approve"
                   data-name="<?= htmlspecialchars(addslashes($r['book_title'])) ?>">
                  <i class="bi bi-check-lg"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal"
                        data-id="<?= $r['id'] ?>"
                        data-name="<?= htmlspecialchars($r['book_title']) ?>">
                  <i class="bi bi-x-lg"></i>
                </button>
              <?php elseif (in_array($r['status'],['borrowed','overdue'])): ?>
                <a href="?return=<?= $r['id'] ?>"
                   class="btn btn-sm btn-outline-primary btn-return"
                   data-title="<?= htmlspecialchars(addslashes($r['book_title'])) ?>">
                  <i class="bi bi-arrow-return-left"></i>
                </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="8">
          <div class="empty-state">
            <i class="bi bi-inbox"></i><p>Tidak ada data peminjaman</p>
          </div>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-end mb-0">
      <?php for ($p=1; $p<=$total_pages; $p++): ?>
        <li class="page-item <?= $p===$page?'active':'' ?>">
          <a class="page-link"
             href="?page=<?= $p ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>">
            <?= $p ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <form method="POST">
        <div class="modal-header">
          <h6 class="modal-title fw-bold text-danger">
            <i class="bi bi-x-circle me-2"></i>Tolak Peminjaman
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="bid" id="rejectBid">
          <p class="text-muted small mb-3">
            Buku: <strong id="rejectBookName"></strong>
          </p>
          <div class="mb-3">
            <label class="form-label fw-600">Alasan Penolakan</label>
            <textarea name="admin_notes" class="form-control" rows="3"
                      placeholder="Contoh: Stok habis, data tidak lengkap..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="reject" class="btn btn-danger">
            <i class="bi bi-x-circle me-1"></i>Tolak
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail -->
<?php if ($detail): ?>
<div class="modal fade show" id="detailModal" tabindex="-1" style="display:block;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h6 class="modal-title fw-bold">
          <i class="bi bi-info-circle me-2 text-info"></i>Detail Peminjaman #<?= $detail['id'] ?>
        </h6>
        <a href="borrowings.php<?= $status_filter?"?status=$status_filter":'' ?>"
           class="btn-close"></a>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="fw-600 text-muted small text-uppercase mb-2">Data Anggota</h6>
            <table class="table table-sm table-borderless">
              <tr><td class="text-muted">Nama</td><td><b><?= htmlspecialchars($detail['user_name']) ?></b></td></tr>
              <tr><td class="text-muted">Email</td><td><?= htmlspecialchars($detail['user_email']) ?></td></tr>
              <tr><td class="text-muted">Telepon</td><td><?= htmlspecialchars($detail['user_phone'] ?? '-') ?></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="fw-600 text-muted small text-uppercase mb-2">Data Buku</h6>
            <table class="table table-sm table-borderless">
              <tr><td class="text-muted">Judul</td><td><b><?= htmlspecialchars($detail['book_title']) ?></b></td></tr>
              <tr><td class="text-muted">Pengarang</td><td><?= htmlspecialchars($detail['book_author']) ?></td></tr>
              <tr><td class="text-muted">ISBN</td><td><?= htmlspecialchars($detail['book_isbn'] ?? '-') ?></td></tr>
            </table>
          </div>
          <div class="col-12">
            <h6 class="fw-600 text-muted small text-uppercase mb-2">Data Peminjaman</h6>
            <table class="table table-sm">
              <tr><td>Status</td><td><?= getStatusBadge($detail['status']) ?></td></tr>
              <tr><td>Tgl Pinjam</td><td><?= formatDate($detail['borrow_date']) ?></td></tr>
              <tr><td>Jatuh Tempo</td><td><?= formatDate($detail['due_date']) ?></td></tr>
              <tr><td>Tgl Kembali</td><td><?= formatDate($detail['return_date']) ?></td></tr>
              <tr><td>Denda</td>
                <td><?= $detail['fine']>0
                  ? '<span class="text-danger fw-bold">'.formatRupiah($detail['fine']).'</span>'
                  : '<span class="text-success">Tidak ada</span>' ?></td></tr>
              <?php if ($detail['notes']): ?>
              <tr><td>Catatan User</td><td><?= htmlspecialchars($detail['notes']) ?></td></tr>
              <?php endif; ?>
              <?php if ($detail['admin_notes']): ?>
              <tr><td>Catatan Admin</td><td class="text-danger"><?= htmlspecialchars($detail['admin_notes']) ?></td></tr>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="borrowings.php<?= $status_filter?"?status=$status_filter":'' ?>"
           class="btn btn-secondary">Tutup</a>
      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<?php
$extra_js = <<<JS
<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('rejectBid').value       = btn.dataset.id;
  document.getElementById('rejectBookName').textContent = btn.dataset.name;
});
</script>
JS;
include '../includes/admin_footer.php';
?>
