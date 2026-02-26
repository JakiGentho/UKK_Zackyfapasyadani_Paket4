<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = 'Kelola Kategori';
$error      = '';

// Tambah
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
    $name = sanitize($_POST['name']        ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    if (!$name) {
        $error = 'Nama kategori wajib diisi!';
    } else {
        $pdo->prepare("INSERT INTO categories (name,description) VALUES (?,?)")
            ->execute([$name, $desc]);
        setFlash('success',"Kategori <b>$name</b> berhasil ditambahkan!");
        header('Location: categories.php'); exit;
    }
}

// Edit
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit'])) {
    $eid  = (int)$_POST['edit_id'];
    $name = sanitize($_POST['edit_name']        ?? '');
    $desc = sanitize($_POST['edit_description'] ?? '');
    if ($name) {
        $pdo->prepare("UPDATE categories SET name=?, description=? WHERE id=?")
            ->execute([$name, $desc, $eid]);
        setFlash('success',"Kategori berhasil diperbarui!");
    }
    header('Location: categories.php'); exit;
}

// Hapus
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category_id=?");
    $cnt->execute([$did]);
    if ($cnt->fetchColumn() > 0) {
        setFlash('warning','Kategori tidak dapat dihapus, masih memiliki buku!');
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$did]);
        setFlash('success','Kategori berhasil dihapus!');
    }
    header('Location: categories.php'); exit;
}

$categories = $pdo->query("
    SELECT c.*, COUNT(b.id) AS book_count
    FROM   categories c
    LEFT JOIN books b ON c.id = b.category_id
    GROUP  BY c.id ORDER BY c.name
")->fetchAll();

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<div class="row g-4">
  <!-- Form Tambah -->
  <div class="col-lg-4">
    <div class="content-card">
      <div class="card-header-custom">
        <h6><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kategori</h6>
      </div>
      <?php if ($error): ?>
        <div class="alert alert-danger auto-dismiss py-2"><?= $error ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-600">Nama Kategori <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-600">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" name="add" class="btn btn-primary w-100">
          <i class="bi bi-save me-1"></i>Simpan
        </button>
      </form>
    </div>
  </div>

  <!-- Daftar Kategori -->
  <div class="col-lg-8">
    <div class="content-card">
      <div class="card-header-custom">
        <h6><i class="bi bi-tags me-2 text-primary"></i>Daftar Kategori</h6>
        <span class="badge bg-secondary"><?= count($categories) ?></span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Nama</th><th>Deskripsi</th><th>Buku</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $i => $c): ?>
            <tr>
              <td class="text-muted"><?= $i+1 ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($c['name']) ?></td>
              <td class="text-muted small">
                <?= $c['description'] ? htmlspecialchars($c['description']) : '-' ?>
              </td>
              <td><span class="badge bg-primary"><?= $c['book_count'] ?></span></td>
              <td>
                <!-- Trigger modal edit -->
                <button class="btn btn-sm btn-outline-warning me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="<?= $c['id'] ?>"
                        data-name="<?= htmlspecialchars($c['name']) ?>"
                        data-desc="<?= htmlspecialchars($c['description'] ?? '') ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="?delete=<?= $c['id'] ?>"
                   class="btn btn-sm btn-outline-danger"
                   data-confirm="Hapus kategori '<?= htmlspecialchars(addslashes($c['name'])) ?>'?">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <form method="POST">
        <div class="modal-header">
          <h6 class="modal-title fw-bold">
            <i class="bi bi-pencil me-2 text-warning"></i>Edit Kategori
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="editId">
          <div class="mb-3">
            <label class="form-label fw-600">Nama Kategori</label>
            <input type="text" name="edit_name" id="editName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Deskripsi</label>
            <textarea name="edit_description" id="editDesc" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="edit" class="btn btn-warning">
            <i class="bi bi-save me-1"></i>Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$extra_js = <<<JS
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('editId').value   = btn.dataset.id;
  document.getElementById('editName').value = btn.dataset.name;
  document.getElementById('editDesc').value = btn.dataset.desc;
});
</script>
JS;
include '../includes/admin_footer.php';
?>
