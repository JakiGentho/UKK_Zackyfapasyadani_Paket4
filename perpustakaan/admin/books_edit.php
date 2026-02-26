<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) { setFlash('danger','Buku tidak ditemukan!'); header('Location: books.php'); exit; }

$page_title = 'Edit Buku';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($_POST['title']       ?? '');
    $author      = sanitize($_POST['author']      ?? '');
    $publisher   = sanitize($_POST['publisher']   ?? '');
    $year        = (int)($_POST['year']           ?? 0);
    $isbn        = sanitize($_POST['isbn']        ?? '');
    $category_id = (int)($_POST['category_id']   ?? 0) ?: null;
    $stock       = max(0, (int)($_POST['stock']   ?? 0));
    $description = sanitize($_POST['description'] ?? '');
    $cover       = $book['cover'];

    $borrowed      = $book['stock'] - $book['available_stock'];
    $new_available = max(0, $stock - $borrowed);

    if (!$title || !$author) {
        $error = 'Judul dan pengarang wajib diisi!';
    } else {
        if (!empty($_FILES['cover']['name'])) {
            $new_cover = uploadCover($_FILES['cover']);
            if ($new_cover) {
                deleteCover($cover);
                $cover = $new_cover;
            } else {
                $error = 'Format cover tidak valid!';
            }
        }
        if (!$error) {
            $stmt = $pdo->prepare("
                UPDATE books
                SET    category_id=?, title=?, author=?, publisher=?, year=?,
                       isbn=?, stock=?, available_stock=?, cover=?, description=?
                WHERE  id=?
            ");
            $stmt->execute([
                $category_id,$title,$author,$publisher,$year,
                $isbn,$stock,$new_available,$cover,$description,$id
            ]);
            setFlash('success',"Buku <b>$title</b> berhasil diperbarui!");
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
    <h6><i class="bi bi-pencil me-2 text-warning"></i>Edit Buku</h6>
    <a href="books.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label fw-600">Judul Buku <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="<?= htmlspecialchars($book['title']) ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"
              <?= $book['category_id']==$c['id']?'selected':'' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Pengarang <span class="text-danger">*</span></label>
        <input type="text" name="author" class="form-control"
               value="<?= htmlspecialchars($book['author']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Penerbit</label>
        <input type="text" name="publisher" class="form-control"
               value="<?= htmlspecialchars($book['publisher'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Tahun Terbit</label>
        <input type="number" name="year" class="form-control"
               value="<?= $book['year'] ?>" min="1900" max="<?= date('Y') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">ISBN</label>
        <input type="text" name="isbn" class="form-control"
               value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600">Jumlah Stok</label>
        <input type="number" name="stock" class="form-control"
               value="<?= $book['stock'] ?>" min="0">
        <small class="text-muted">
          Sedang dipinjam: <?= $book['stock'] - $book['available_stock'] ?>
        </small>
      </div>
      <div class="col-md-5">
        <label class="form-label fw-600">Ganti Cover</label>
        <?php if ($book['cover']): ?>
          <div class="mb-2">
            <img src="<?= BASE_URL ?>uploads/covers/<?= $book['cover'] ?>"
                 height="90" style="border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15);"
                 id="coverPreview">
          </div>
        <?php else: ?>
          <img id="coverPreview" src="#" alt="Preview"
               style="display:none;height:90px;border-radius:8px;margin-bottom:8px;">
        <?php endif; ?>
        <input type="file" name="cover" id="coverInput" class="form-control" accept="image/*">
        <small class="text-muted">Kosongkan jika tidak ingin mengganti cover</small>
      </div>
      <div class="col-12">
        <label class="form-label fw-600">Deskripsi</label>
        <textarea name="description" class="form-control"
                  rows="4"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
      </div>
      <div class="col-12 pt-2">
        <button type="submit" class="btn btn-warning me-2">
          <i class="bi bi-save me-1"></i>Update Buku
        </button>
        <a href="books.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>

<?php include '../includes/admin_footer.php'; ?>
