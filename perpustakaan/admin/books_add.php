<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = 'Tambah Buku';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($_POST['title']       ?? '');
    $author      = sanitize($_POST['author']      ?? '');
    $publisher   = sanitize($_POST['publisher']   ?? '');
    $year        = (int)($_POST['year']           ?? 0);
    $isbn        = sanitize($_POST['isbn']        ?? '');
    $category_id = (int)($_POST['category_id']   ?? 0) ?: null;
    $stock       = max(1, (int)($_POST['stock']   ?? 1));
    $description = sanitize($_POST['description'] ?? '');
    $cover       = null;

    if (!$title || !$author) {
        $error = 'Judul dan pengarang wajib diisi!';
    } else {
        if (!empty($_FILES['cover']['name'])) {
            $cover = uploadCover($_FILES['cover']);
            if (!$cover) $error = 'Format cover tidak valid (maks 2MB, jpg/png/gif/webp)!';
        }
        if (!$error) {
            $stmt = $pdo->prepare("
                INSERT INTO books
                  (category_id,title,author,publisher,year,isbn,stock,available_stock,cover,description)
                VALUES (?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([$category_id,$title,$author,$publisher,$year,$isbn,$stock,$stock,$cover,$description]);
            setFlash('success', "Buku <b>$title</b> berhasil ditambahkan!");
            header('Location: books.php');
            exit;
        }
    }
}

include '../includes/admin_sidebar.php';
?>

<?php if ($error): ?>
  <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
<?php endif; ?>

<div class="content-card">
  <div class="card-header-custom">
    <h6><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Buku Baru</h6>
    <a href="books.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label fw-600">Judul Buku <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="<?= sanitize($_POST['title'] ?? '') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"
              <?= ($_POST['category_id']??'')==$c['id']?'selected':'' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Pengarang <span class="text-danger">*</span></label>
        <input type="text" name="author" class="form-control"
               value="<?= sanitize($_POST['author'] ?? '') ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Penerbit</label>
        <input type="text" name="publisher" class="form-control"
               value="<?= sanitize($_POST['publisher'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Tahun Terbit</label>
        <input type="number" name="year" class="form-control"
               value="<?= $_POST['year'] ?? date('Y') ?>"
               min="1900" max="<?= date('Y') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">ISBN</label>
        <input type="text" name="isbn" class="form-control"
               value="<?= sanitize($_POST['isbn'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Jumlah Stok <span class="text-danger">*</span></label>
        <input type="number" name="stock" class="form-control"
               value="<?= $_POST['stock'] ?? 1 ?>" min="1" required>
      </div>
      <div class="col-md-5">
        <label class="form-label fw-600">Cover Buku</label>
        <input type="file" name="cover" id="coverInput"
               class="form-control" accept="image/*">
        <small class="text-muted">JPG/PNG/GIF/WEBP, maks 2MB</small>
        <div class="mt-2">
          <img id="coverPreview" src="#" alt="Preview"
               style="display:none;height:100px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15);">
        </div>
      </div>
      <div class="col-12">
        <label class="form-label fw-600">Deskripsi</label>
        <textarea name="description" class="form-control" rows="4"
        ><?= sanitize($_POST['description'] ?? '') ?></textarea>
      </div>
      <div class="col-12 pt-2">
        <button type="submit" class="btn btn-primary me-2">
          <i class="bi bi-save me-1"></i>Simpan Buku
        </button>
        <a href="books.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>

<?php include '../includes/admin_footer.php'; ?>
