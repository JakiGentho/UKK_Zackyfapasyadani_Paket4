<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireUser();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT b.*, c.name AS category_name
    FROM   books b
    LEFT JOIN categories c ON b.category_id=c.id
    WHERE  b.id=?
");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    setFlash('danger','Buku tidak ditemukan!');
    header('Location: books.php'); exit;
}

// Cek sudah pinjam buku ini
$cek = $pdo->prepare(
    "SELECT id FROM borrowings
     WHERE user_id=? AND book_id=? AND status IN ('pending','approved','borrowed')"
);
$cek->execute([$_SESSION['user_id'], $id]);
if ($cek->fetch()) {
    setFlash('warning','Anda sudah meminjam atau mengajukan buku ini!');
    header('Location: books.php'); exit;
}

$page_title = 'Ajukan Peminjaman';
$error      = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $notes = sanitize($_POST['notes'] ?? '');
    if ($book['available_stock'] <= 0) {
        $error = 'Maaf, stok buku sudah habis!';
    } else {
        $pdo->prepare(
            "INSERT INTO borrowings (user_id,book_id,notes,status,created_at)
             VALUES (?,?,?,'pending',NOW())"
        )->execute([$_SESSION['user_id'], $id, $notes]);
        setFlash('success',
            "Permohonan pinjam buku <b>{$book['title']}</b> berhasil diajukan! ".
            "Tunggu konfirmasi admin."
        );
        header('Location: history.php'); exit;
    }
}

include '../includes/user_navbar.php';
?>

<?php showFlash(); ?>
<?php if ($error): ?>
  <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="user-card">
      <!-- Info Buku -->
      <div class="d-flex gap-4 mb-4 p-4 rounded-3"
           style="background:linear-gradient(135deg,#f8f9fa,#e9ecef);">
        <?php if ($book['cover']): ?>
          <img src="<?= BASE_URL ?>uploads/covers/<?= $book['cover'] ?>"
               height="130" style="object-fit:cover;border-radius:10px;
               box-shadow:0 4px 15px rgba(0,0,0,.15);">
        <?php else: ?>
          <div style="width:90px;height:130px;background:linear-gradient(135deg,#667eea,#764ba2);
                      border-radius:10px;display:flex;align-items:center;justify-content:center;
                      color:rgba(255,255,255,.6);font-size:2.5rem;flex-shrink:0;">
            <i class="bi bi-book"></i>
          </div>
        <?php endif; ?>
        <div>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($book['title']) ?></h5>
          <p class="text-muted mb-2"><?= htmlspecialchars($book['author']) ?></p>
          <?php if ($book['category_name']): ?>
            <span class="badge bg-light text-dark border me-2">
              <?= htmlspecialchars($book['category_name']) ?>
            </span>
          <?php endif; ?>
          <?php if ($book['publisher']): ?>
            <span class="badge bg-light text-dark border me-2">
              <?= htmlspecialchars($book['publisher']) ?>
            </span>
          <?php endif; ?>
          <?php if ($book['year']): ?>
            <span class="badge bg-light text-dark border"><?= $book['year'] ?></span>
          <?php endif; ?>
          <div class="mt-2">
            <span class="badge bg-success">
              <i class="bi bi-check-circle me-1"></i>
              <?= $book['available_stock'] ?> tersedia
            </span>
          </div>
          <?php if ($book['description']): ?>
            <p class="text-muted small mt-2 mb-0">
              <?= htmlspecialchars(substr($book['description'], 0, 150)) ?>...
            </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Info Peminjaman -->
      <div class="alert alert-info d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
        <div class="small">
          <strong>Informasi Peminjaman:</strong>
          <ul class="mb-0 mt-1">
            <li>Durasi peminjaman: <strong><?= LOAN_DAYS ?> hari</strong></li>
            <li>Denda keterlambatan: <strong><?= formatRupiah(FINE_PER_DAY) ?>/hari</strong></li>
            <li>Permohonan akan dikonfirmasi oleh admin</li>
          </ul>
        </div>
      </div>

      <!-- Form -->
      <form method="POST">
        <div class="mb-4">
          <label class="form-label fw-600">
            Catatan <span class="text-muted small">(opsional)</span>
          </label>
          <textarea name="notes" class="form-control" rows="3"
                    placeholder="Contoh: Butuh untuk tugas, mohon segera diproses, dll."
          ><?= sanitize($_POST['notes'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-gradient flex-fill">
            <i class="bi bi-send me-2"></i>Ajukan Peminjaman
          </button>
          <a href="books.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '../includes/user_footer.php'; ?>
