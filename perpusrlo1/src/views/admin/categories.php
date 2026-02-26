<?php 
$page_title = 'Kelola Kategori - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-tags"></i> Kelola Kategori</h2>
    
    <div class="row">
        <!-- Form Tambah Kategori -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/index.php?page=admin/category/store" method="POST">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- List Kategori -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Kategori</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p class="text-muted text-center">Belum ada kategori</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><strong><?php echo $category['nama_kategori']; ?></strong></td>
                                        <td><?php echo $category['deskripsi'] ?? '-'; ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/index.php?page=admin/category/delete&id=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus kategori ini?')">
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
    </div>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
