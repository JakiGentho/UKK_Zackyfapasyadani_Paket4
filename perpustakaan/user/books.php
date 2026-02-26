<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireUser();

$page_title  = 'Katalog Buku';
$search      = sanitize($_GET['search']   ?? '');
$cat_id      = (int)($_GET['category']   ?? 0);
$avail_only  = isset($_GET['available']);
$page        = max(1,(int)($_GET['page'] ?? 1));
$per_page    = 12;

$where  = '1=1';
$params = [];
if ($search) {
    $where   .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.publisher LIKE ?)";
    $params   = array_merge($params,["%$search%","%$search%","%$search%"]);
}
if ($cat_id) {
    $where   .= " AND b.category_id=?";
    $params[] = $cat_id;
}
if ($avail_only) {
    $where .= " AND b.available_stock > 0";
}

$total = $pdo->prepare("SELECT COUNT(*) FROM books b WHERE $where");
$total->execute($params);
$total_rows  = (int)$total->fetchColumn();
$total_pages = max(1,(int)ceil($total_rows/$per_page));
$offset      = ($page-1)*$per_page;

$stmt = $pdo->prepare("
    SELECT b.*, c.name AS category_name
    FROM   books b
    LEFT JOIN categories c ON b.category_id=c.id
    WHERE  $where
    ORDER  BY b.title ASC
    LIMIT  $per_page OFFSET $offset
");
$stmt->execute($params);
$books = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Buku yang sedang dalam proses pinjam user ini
$user_borrowing_ids = $pdo->prepare("
    SELECT book_id FROM borrowings
    WHERE  user_id=? AND status IN ('pending','approved','borrowed')
");
$user_borrowing_ids->execute([$_SESSION['user_id']]);
$borrowed_ids = array_column($user_borrowing_ids->fetchAll(), 'book_id');

include '../includes/user_navbar.php';
?>

<?php showFlash(); ?>

<!-- Search & Filter -->
<div class="search-bar">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label fw-600 small">Cari Buku</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="search" class="form-control"
               placeholder="Judul, pengarang, penerbit..."
               value="<?= htmlspecialchars($search) ?>">
      </div>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-600 small">Kategori</label>
      <select name="category" class="form-select">
        <option value="">Semua Kategori</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cat_id==$c['id']?'selected':'' ?>>
            <?= htmlspecialchars($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex align-items-center gap-2 pt-3">
      <input class="form-check-input mt-0" type="checkbox" name="available"
             id="avail" <?= $avail_only?'checked':'' ?>>
      <label class="form-check-label small" for="avail">Tersedia saja</label>
    </div>
    <div class="col-md-2 d-flex gap-1">
      <button class="btn btn-gradient flex-fill"><i class="bi bi-search"></i></button>
      <a href="books.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<!-- Kategori Filter Buttons -->
<div class="d-flex flex-wrap gap-2 mb-3">
  <a href="?<?= $search?"search=".urlencode($search)."&":'' ?>"
     class="btn btn-sm <?= !$cat_id?'btn-gradient':'btn-outline-secondary' ?>">
    Semua
  </a>
  <?php foreach ($categories as $c): ?>
    <a href="?category=<?= $c['id'] ?><?= $search?"&search=".urlencode($search):'' ?>"
       class="btn btn-sm <?= $cat_id==$c['id']?'btn-gradient':'btn-outline-secondary' ?>">
      <?= htmlspecialchars($c['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- Hasil -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <small class="text-muted">
    Menampilkan <?= count($books) ?> dari <?= $total_rows ?> buku
  </small>
  <small class="text-muted">Halaman <?= $page ?> / <?= $total_pages ?></small>
</div>

<!-- Grid Buku -->
<?php if ($books): ?>
<div class="row g-3 mb-4" id="bookGrid">
  <?php foreach ($books as $b): ?>
  <div class="col-6 col-md-4 col-lg-3 book-item"
       data-cat="<?= $b['category_id'] ?>">
    <div class="card book-card">
      <?php if ($b['cover']): ?>
        <img src="<?= BASE_URL ?>uploads/covers/<?= $b['cover'] ?>"
             class="book-cover" alt="<?= htmlspecialchars($b['title']) ?>">
      <?php else: ?>
        <div class="book-cover-placeholder">
          <i class="bi bi-book-half"></i>
        </div>
      <?php endif; ?>
      <div class="card-body p-3">
        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
        <div class="book-author"><?= htmlspecialchars($b['author']) ?></div>
        <?php if ($b['category_name']): ?>
          <span class="badge bg-light text-dark border mb-2" style="font-size:.7rem;">
            <?= htmlspecialchars($b['category_name']) ?>
          </span>
        <?php endif; ?>
        <div class="book-footer">
          <span class="stock-badge <?= $b['available_stock']>0?'bg-success text-white':'bg-danger text-white' ?>">
            <?= $b['available_stock']>0
              ? "<i class='bi bi-check-circle me-1'></i>{$b['available_stock']} tersedia"
              : "<i class='bi bi-x-circle me-1'></i>Habis" ?>
          </span>
        </div>
        <div class="mt-2">
          <?php if (in_array($b['id'], $borrowed_ids)): ?>
            <button class="btn btn-secondary btn-sm w-100" disabled>
              <i class="bi bi-clock me-1"></i>Sedang Dipinjam
            </button>
          <?php elseif ($b['available_stock'] > 0): ?>
            <a href="<?= BASE_URL ?>user/borrow.php?id=<?= $b['id'] ?>"
               class="btn btn-gradient btn-sm w-100 btn-borrow"
               data-title="<?= htmlspecialchars($b['title']) ?>">
              <i class="bi bi-book-half me-1"></i>Pinjam
            </a>
          <?php else: ?>
            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
              <i class="bi bi-x me-1"></i>Stok Habis
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
  <i class="bi bi-search"></i>
  <p>Tidak ada buku yang ditemukan</p>
  <a href="books.php" class="btn btn-outline-primary btn-sm mt-2">Reset Pencarian</a>
</div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<nav class="mt-3">
  <ul class="pagination justify-content-center">
    <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
    <?php endif; ?>
    <?php for ($p=1; $p<=$total_pages; $p++): ?>
      <li class="page-item <?= $p===$page?'active':'' ?>">
        <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>">
          <?= $p ?>
        </a>
      </li>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    <?php endif; ?>
  </ul>
</nav>
<?php endif; ?>

<?php include '../includes/user_footer.php'; ?>
