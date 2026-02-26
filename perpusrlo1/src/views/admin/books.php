<?php 
$page_title = 'Kelola Buku - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-book"></i> Kelola Buku</h2>
        <a href="<?php echo BASE_URL; ?>/index.php?page=admin/book/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Buku
        </a>
    </div>
    
    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="page" value="admin/books">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul, pengarang, atau ISBN..." 
                               value="<?php echo $_GET['search'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo $cat['nama_kategori']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Books Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($books)): ?>
                <p class="text-muted text-center">Tidak ada buku ditemukan</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>ISBN</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($book['cover_image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/assets/images/covers/<?php echo $book['cover_image']; ?>" 
                                             alt="Cover" style="width: 50px; height: 70px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 70px; font-size: 10px;">
                                            No Cover
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $book['isbn'] ?? '-'; ?></td>
                                <td><?php echo $book['judul']; ?></td>
                                <td><?php echo $book['pengarang']; ?></td>
                                <td><?php echo $book['nama_kategori'] ?? '-'; ?></td>
                                <td><?php echo $book['stok']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $book['tersedia'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $book['tersedia']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin/book/edit&id=<?php echo $book['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin/book/delete&id=<?php echo $book['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
