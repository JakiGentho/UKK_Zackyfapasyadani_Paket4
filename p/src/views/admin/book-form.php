<?php
$title = isset($book) ? "Edit Buku" : "Tambah Buku";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2><?php echo $title; ?></h2>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="kode_buku">Kode Buku *</label>
                <input type="text" id="kode_buku" name="kode_buku" 
                       value="<?php echo escape($book['kode_buku'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="judul">Judul *</label>
                <input type="text" id="judul" name="judul" 
                       value="<?php echo escape($book['judul'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="pengarang">Pengarang *</label>
                <input type="text" id="pengarang" name="pengarang" 
                       value="<?php echo escape($book['pengarang'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" 
                       value="<?php echo escape($book['penerbit'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" 
                       min="1901" max="2155"
                       placeholder="Contoh: 2024"
                       value="<?php echo escape($book['tahun_terbit'] ?? ''); ?>">
                <small style="color: #666; font-size: 0.85rem;">Opsional. Tahun antara 1901-2155</small>
            </div>
            
            <div class="form-group">
                <label for="stok">Stok *</label>
                <input type="number" id="stok" name="stok" 
                       min="0"
                       value="<?php echo escape($book['stok'] ?? 0); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                            <?php echo (isset($book) && $book['kategori_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($category['nama_kategori']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo escape($book['deskripsi'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="?page=books" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
