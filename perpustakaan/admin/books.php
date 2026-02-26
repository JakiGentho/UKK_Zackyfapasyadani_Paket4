<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = 'Kelola Buku';
$search     = sanitize($_GET['search']   ?? '');
$cat_id     = (int)($_GET['category']    ?? 0);
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 10;

$where  = '1=1';
$params = [];
if ($search) {
    $where   .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
    $params   = array_merge($params, ["%$search%","%$search%","%$search%"]);
}
if ($cat_id) {
    $where   .= " AND b.category_id = ?";
    $params[] = $cat_id;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM books b WHERE $where");
$countStmt->execute($params);
$total_rows  = (int)$countStmt->fetchColumn();
$total_pages = max(1, (int)ceil($total_rows / $per_page));
$offset      = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT b.*, c.name AS category_name
    FROM   books b
    LEFT JOIN categories c ON b.category_id = c.id
    WHERE  $where
    ORDER  BY b.created_at DESC
    LIMIT  $per_page OFFSET $offset
");
$stmt->execute($params);
$books = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<div class="content-card">
  <div class="card-header-custom">
    <h6><i class="bi bi-book me-2 text-primary"></i>Daftar Buku
      <span class="badge bg-secondary ms-2"><?= $total_rows ?></span>
    </h6>
    <a href="<?= BASE_URL ?>admin/books_add.php" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Tambah Buku
    </a>
  </div>

  <!-- Filter -->
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="Cari judul, pengarang, ISBN..."
             value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-4">
      <select name="category" class="form-select form-select-sm">
        <option value="">Semua Kategori</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cat_id==$c['id']?'selected':'' ?>>
            <?= htmlspecialchars($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
      <button class="btn btn-outline-primary btn-sm flex-fill"><i class="bi bi-search"></i> Cari</button>
      <a href="books.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th><th>Cover</th><th>Judul & Pengarang</th>
          <th>Kategori</th><th>ISBN</th><th>Stok</th><th>Tersedia</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($books): foreach ($books as $i => $b): ?>
        <tr>
          <td class="text-muted"><?= ($offset+$i+1) ?></td>
          <td>
            <?php if ($b['cover']): ?>
              <img src="<?= BASE_URL ?>uploads/covers/<?= $b['cover'] ?>"
                   class="book-thumb" alt="cover">
            <?php else: ?>
              <div class="book-thumb-placeholder"><i class="bi bi-book"></i></div>
            <?php endif; ?>
          </td>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($b['title']) ?></div>
            <small class="text-muted">
              <?= htmlspecialchars($b['author']) ?>
              <?= $b['year'] ? "· {$b['year']}" : '' ?>
            </small>
          </td>
          <td>
            <span class="badge bg-light text-dark border">
              <?= htmlspecialchars($b['category_name'] ?? 'Tanpa Kategori') ?>
            </span>
          </td>
          <td><small><?= htmlspecialchars($b['isbn'] ?: '-') ?></small></td>
          <td class="text-center"><?= $b['stock'] ?></td>
          <td class="text-center">
            <span class="badge <?= $b['available_stock']>0 ? 'bg-success':'bg-danger' ?>">
              <?= $b['available_stock'] ?>
            </span>
          </td>
          <td>
            <a href="books_edit.php?id=<?= $b['id'] ?>"
               class="btn btn-sm btn-outline-warning me-1"
               data-bs-toggle="tooltip" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="books_delete.php?id=<?= $b['id'] ?>"
               class="btn btn-sm btn-outline-danger"
               data-confirm="Hapus buku '<?= htmlspecialchars(addslashes($b['title'])) ?>'?"
               data-bs-toggle="tooltip" title="Hapus">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="8">
          <div class="empty-state">
            <i class="bi bi-search"></i><p>Tidak ada buku ditemukan</p>
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
      <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <li class="page-item <?= $p===$page?'active':'' ?>">
          <a class="page-link"
             href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>">
            <?= $p ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>

<?php include '../includes/admin_footer.php'; ?>
