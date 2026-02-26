<?php 
$page_title = 'Katalog Buku - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-book"></i> Katalog Buku</h2>
    
    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="page" value="user/catalog">
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
    
    <!-- Books Grid -->
    <?php if (empty($books)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Tidak ada buku ditemukan
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($book['cover_image'])): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/images/covers/<?php echo $book['cover_image']; ?>" 
                             class="card-img-top" alt="Cover" style="height: 300px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="height: 300px;">
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['judul']; ?></h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="bi bi-person"></i> <?php echo $book['pengarang']; ?><br>
                                <i class="bi bi-tag"></i> <?php echo $book['nama_kategori'] ?? 'Tanpa Kategori'; ?><br>
                                <i class="bi bi-calendar"></i> <?php echo $book['tahun_terbit'] ?? '-'; ?>
                            </small>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php echo $book['tersedia'] > 0 ? 'success' : 'danger'; ?>">
                                <?php echo $book['tersedia']; ?> Tersedia
                            </span>
                            
                            <?php if ($book['tersedia'] > 0): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?page=user/loan/request&book_id=<?php echo $book['id']; ?>" 
                                   class="btn btn-sm btn-primary"
                                   onclick="return confirm('Ajukan peminjaman untuk buku ini?')">
                                    <i class="bi bi-plus-circle"></i> Pinjam
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled>
                                    Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($book['deskripsi'])): ?>
                    <div class="card-footer">
                        <small class="text-muted">
                            <?php echo substr($book['deskripsi'], 0, 100); ?><?php echo strlen($book['deskripsi']) > 100 ? '...' : ''; ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
