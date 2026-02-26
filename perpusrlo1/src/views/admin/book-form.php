<?php 
$page_title = (isset($book) && $book) ? 'Edit Buku' : 'Tambah Buku';
$page_title .= ' - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';

$is_edit = isset($book) && $book;
$form_action = $is_edit ? 'admin/book/update&id=' . $book['id'] : 'admin/book/store';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-<?php echo $is_edit ? 'pencil' : 'plus-circle'; ?>"></i>
                        <?php echo $is_edit ? 'Edit Buku' : 'Tambah Buku'; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/index.php?page=<?php echo $form_action; ?>" 
                          method="POST" enctype="multipart/form-data">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" 
                                       value="<?php echo $book['isbn'] ?? ($_SESSION['old_input']['isbn'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo (isset($book) && $book['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo $cat['nama_kategori']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Buku *</label>
                            <input type="text" class="form-control" id="judul" name="judul" 
                                   value="<?php echo $book['judul'] ?? ($_SESSION['old_input']['judul'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pengarang" class="form-label">Pengarang *</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang" 
                                       value="<?php echo $book['pengarang'] ?? ($_SESSION['old_input']['pengarang'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="penerbit" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                       value="<?php echo $book['penerbit'] ?? ($_SESSION['old_input']['penerbit'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" 
                                       value="<?php echo $book['tahun_terbit'] ?? ($_SESSION['old_input']['tahun_terbit'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="stok" class="form-label">Stok *</label>
                                <input type="number" class="form-control" id="stok" name="stok" 
                                       value="<?php echo $book['stok'] ?? ($_SESSION['old_input']['stok'] ?? '1'); ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                                <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" 
                                       value="<?php echo $book['lokasi_rak'] ?? ($_SESSION['old_input']['lokasi_rak'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Buku</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                            <?php if (isset($book) && !empty($book['cover_image'])): ?>
                                <img src="<?php echo BASE_URL; ?>/assets/images/covers/<?php echo $book['cover_image']; ?>" 
                                     alt="Current Cover" class="mt-2" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?php echo $book['deskripsi'] ?? ($_SESSION['old_input']['deskripsi'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                            </button>
                            <a href="<?php echo BASE_URL; ?>/index.php?page=admin/books" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['old_input']);
require_once ROOT_PATH . '/src/views/layouts/footer.php'; 
?>
