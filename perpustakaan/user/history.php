<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireUser();
updateOverdue($pdo);

$page_title    = 'Riwayat Peminjaman';
$uid           = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? '';
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 10;

$where  = "br.user_id = ?";
$params = [$uid];
if ($status_filter && in_array($status_filter,
    ['pending','approved','borrowed','returned','overdue','rejected'], true)) {
    $where   .= " AND br.status = ?";
    $params[] = $status_filter;
}

$total = $pdo->prepare(
    "SELECT COUNT(*) FROM borrowings br
     JOIN books b ON br.book_id=b.id
     WHERE $where"
);
$total->execute($params);
$total_rows  = (int)$total->fetchColumn();
$total_pages = max(1, (int)ceil($total_rows / $per_page));
$offset      = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT br.*, b.title AS book_title, b.author AS book_author,
           b.cover AS book_cover, c.name AS category_name
    FROM   borrowings br
    JOIN   books b ON br.book_id = b.id
    LEFT JOIN categories c ON b.category_id = c.id
    WHERE  $where
    ORDER  BY br.created_at DESC
    LIMIT  $per_page OFFSET $offset
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Hitung per status untuk badge tab
$counts = [];
foreach (['pending','borrowed','overdue','returned','rejected'] as $s) {
    $c = $pdo->prepare(
        "SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status=?"
    );
    $c->execute([$uid, $s]);
    $counts[$s] = (int)$c->fetchColumn();
}

// Total denda user
$total_fine = $pdo->prepare(
    "SELECT COALESCE(SUM(fine),0) FROM borrowings WHERE user_id=? AND status='returned'"
);
$total_fine->execute([$uid]);
$total_fine = (float)$total_fine->fetchColumn();

include '../includes/user_navbar.php';
?>

<?php showFlash(); ?>

<!-- Ringkasan -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-box stat-primary">
      <span class="stat-icon"><i class="bi bi-book-half"></i></span>
      <div class="stat-number"><?= $counts['borrowed'] + $counts['overdue'] ?></div>
      <div class="stat-label">Aktif</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-warning">
      <span class="stat-icon"><i class="bi bi-hourglass-split"></i></span>
      <div class="stat-number"><?= $counts['pending'] ?></div>
      <div class="stat-label">Menunggu</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-success">
      <span class="stat-icon"><i class="bi bi-check2-all"></i></span>
      <div class="stat-number"><?= $counts['returned'] ?></div>
      <div class="stat-label">Dikembalikan</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-box stat-danger">
      <span class="stat-icon"><i class="bi bi-cash-coin"></i></span>
      <div class="stat-number" style="font-size:1rem;"><?= formatRupiah($total_fine) ?></div>
      <div class="stat-label">Total Denda</div>
    </div>
  </div>
</div>

<!-- Tab Filter -->
<div class="user-card mb-0 pb-0" style="border-radius:12px 12px 0 0;">
  <div class="d-flex flex-wrap gap-2">
    <a href="history.php"
       class="btn btn-sm <?= !$status_filter ? 'btn-dark' : 'btn-outline-secondary' ?>">
      Semua
      <span class="badge bg-secondary ms-1"><?= array_sum($counts) ?></span>
    </a>
    <?php
    $tabs = [
      'pending'  => ['warning', 'Menunggu'],
      'borrowed' => ['primary', 'Dipinjam'],
      'overdue'  => ['danger',  'Terlambat'],
      'returned' => ['success', 'Dikembalikan'],
      'rejected' => ['secondary','Ditolak'],
    ];
    foreach ($tabs as $s => [$color, $label]):
    ?>
      <a href="?status=<?= $s ?>"
         class="btn btn-sm <?= $status_filter===$s ? "btn-$color" : "btn-outline-$color" ?>">
        <?= $label ?>
        <?php if ($counts[$s] ?? 0): ?>
          <span class="badge bg-white text-dark ms-1"><?= $counts[$s] ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- Tabel Riwayat -->
<div class="user-card" style="border-radius:0 0 12px 12px; margin-top:0; padding-top:20px;">
  <?php if ($rows): ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle small">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Buku</th>
          <th>Tgl Ajuan</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Tgl Kembali</th>
          <th>Status</th>
          <th>Denda</th>
          <th>Detail</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td class="text-muted"><?= ($offset + $i + 1) ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <?php if ($r['book_cover']): ?>
                <img src="<?= BASE_URL ?>uploads/covers/<?= $r['book_cover'] ?>"
                     width="36" height="48"
                     style="object-fit:cover;border-radius:5px;flex-shrink:0;">
              <?php else: ?>
                <div style="width:36px;height:48px;background:linear-gradient(135deg,#667eea,#764ba2);
                            border-radius:5px;display:flex;align-items:center;justify-content:center;
                            color:rgba(255,255,255,.6);flex-shrink:0;">
                  <i class="bi bi-book" style="font-size:.8rem;"></i>
                </div>
              <?php endif; ?>
              <div>
                <div class="fw-semibold"><?= htmlspecialchars($r['book_title']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($r['book_author']) ?></small>
              </div>
            </div>
          </td>
          <td><?= formatDate($r['created_at']) ?></td>
          <td><?= formatDate($r['borrow_date']) ?></td>
          <td>
            <?php if ($r['due_date'] && in_array($r['status'], ['borrowed','overdue'])): ?>
              <span data-due-date="<?= $r['due_date'] ?>"><?= formatDate($r['due_date']) ?></span>
            <?php else: ?>
              <?= formatDate($r['due_date']) ?>
            <?php endif; ?>
          </td>
          <td><?= formatDate($r['return_date']) ?></td>
          <td><?= getStatusBadge($r['status']) ?></td>
          <td>
            <?php if ($r['fine'] > 0): ?>
              <span class="text-danger fw-semibold"><?= formatRupiah($r['fine']) ?></span>
            <?php elseif (in_array($r['status'], ['borrowed','overdue'])): ?>
              <?php $late = getDaysLate($r['due_date']); ?>
              <?php if ($late > 0): ?>
                <span class="text-warning fw-semibold small">
                  <?= formatRupiah(calculateFine($late)) ?> (estimasi)
                </span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td>
            <button class="btn btn-sm btn-outline-info"
                    data-bs-toggle="modal"
                    data-bs-target="#detailModal"
                    data-id="<?= $r['id'] ?>"
                    data-book="<?= htmlspecialchars($r['book_title']) ?>"
                    data-author="<?= htmlspecialchars($r['book_author']) ?>"
                    data-borrow="<?= formatDate($r['borrow_date']) ?>"
                    data-due="<?= formatDate($r['due_date']) ?>"
                    data-return="<?= formatDate($r['return_date']) ?>"
                    data-status="<?= getStatusBadge($r['status']) ?>"
                    data-fine="<?= $r['fine'] > 0 ? formatRupiah($r['fine']) : '-' ?>"
                    data-notes="<?= htmlspecialchars($r['notes'] ?? '-') ?>"
                    data-admin-notes="<?= htmlspecialchars($r['admin_notes'] ?? '-') ?>">
              <i class="bi bi-eye"></i>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center mb-0">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link"
             href="?page=<?= $page-1 ?>&status=<?= urlencode($status_filter) ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>
      <?php endif; ?>
      <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <li class="page-item <?= $p===$page?'active':'' ?>">
          <a class="page-link"
             href="?page=<?= $p ?>&status=<?= urlencode($status_filter) ?>">
            <?= $p ?>
          </a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?>
        <li class="page-item">
          <a class="page-link"
             href="?page=<?= $page+1 ?>&status=<?= urlencode($status_filter) ?>">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

  <?php else: ?>
  <div class="empty-state">
    <i class="bi bi-clock-history"></i>
    <p>
      <?= $status_filter
        ? "Tidak ada riwayat dengan status ini"
        : "Belum ada riwayat peminjaman" ?>
    </p>
    <?php if (!$status_filter): ?>
      <a href="<?= BASE_URL ?>user/books.php"
         class="btn btn-gradient btn-sm mt-2">
        <i class="bi bi-book me-1"></i>Mulai Pinjam Buku
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h6 class="modal-title fw-bold">
          <i class="bi bi-info-circle me-2 text-info"></i>Detail Peminjaman
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless table-sm small">
          <tr>
            <td class="text-muted" width="40%">Judul Buku</td>
            <td class="fw-semibold" id="dBook"></td>
          </tr>
          <tr>
            <td class="text-muted">Pengarang</td>
            <td id="dAuthor"></td>
          </tr>
          <tr><td colspan="2"><hr class="my-1"></td></tr>
          <tr>
            <td class="text-muted">Status</td>
            <td id="dStatus"></td>
          </tr>
          <tr>
            <td class="text-muted">Tgl Pinjam</td>
            <td id="dBorrow"></td>
          </tr>
          <tr>
            <td class="text-muted">Jatuh Tempo</td>
            <td id="dDue"></td>
          </tr>
          <tr>
            <td class="text-muted">Tgl Kembali</td>
            <td id="dReturn"></td>
          </tr>
          <tr>
            <td class="text-muted">Denda</td>
            <td id="dFine" class="fw-semibold text-danger"></td>
          </tr>
          <tr id="rowNotes">
            <td class="text-muted">Catatan Saya</td>
            <td id="dNotes"></td>
          </tr>
          <tr id="rowAdminNotes">
            <td class="text-muted">Catatan Admin</td>
            <td id="dAdminNotes" class="text-danger"></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php
$extra_js = <<<JS
<script>
document.getElementById('detailModal').addEventListener('show.bs.modal', function(e) {
  const b = e.relatedTarget;
  document.getElementById('dBook').textContent    = b.dataset.book;
  document.getElementById('dAuthor').textContent  = b.dataset.author;
  document.getElementById('dStatus').innerHTML    = b.dataset.status;
  document.getElementById('dBorrow').textContent  = b.dataset.borrow;
  document.getElementById('dDue').textContent     = b.dataset.due;
  document.getElementById('dReturn').textContent  = b.dataset.return;
  document.getElementById('dFine').textContent    = b.dataset.fine;

  const notes = b.dataset.notes;
  const adminNotes = b.dataset.adminNotes;
  document.getElementById('dNotes').textContent      = notes;
  document.getElementById('dAdminNotes').textContent = adminNotes;
  document.getElementById('rowNotes').style.display      = notes && notes !== '-' ? '' : 'none';
  document.getElementById('rowAdminNotes').style.display = adminNotes && adminNotes !== '-' ? '' : 'none';
});
</script>
JS;
include '../includes/user_footer.php';
?>
