<?php 
$page_title = 'Dashboard - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Selamat Datang, <?php echo getUsername(); ?>! <i class="bi bi-hand-wave"></i></h2>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Peminjaman Aktif</h6>
                            <h2 class="mb-0"><?php echo $active_loans; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-journal-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Peminjaman</h6>
                            <h2 class="mb-0"><?php echo count($all_loans); ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Books -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-book"></i> Buku Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($recent_books as $book): ?>
                <div class="col-md-2 mb-3">
                    <div class="card h-100">
                        <?php if (!empty($book['cover_image'])): ?>
                            <img src="<?php echo BASE_URL; ?>/assets/images/covers/<?php echo $book['cover_image']; ?>" 
                                 class="card-img-top" alt="Cover" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-book fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem;"><?php echo substr($book['judul'], 0, 30); ?><?php echo strlen($book['judul']) > 30 ? '...' : ''; ?></h6>
                            <p class="card-text mb-1" style="font-size: 0.8rem;">
                                <small class="text-muted"><?php echo $book['pengarang']; ?></small>
                            </p>
                            <span class="badge bg-<?php echo $book['tersedia'] > 0 ? 'success' : 'danger'; ?>">
                                <?php echo $book['tersedia'] > 0 ? 'Tersedia' : 'Tidak Tersedia'; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>/index.php?page=user/catalog" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> Lihat Semua Buku
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
