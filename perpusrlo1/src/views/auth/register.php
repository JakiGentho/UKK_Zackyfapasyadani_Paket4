<?php 
$page_title = 'Register - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="bi bi-person-plus text-primary"></i> Register
                    </h2>
                    
                    <form action="<?php echo BASE_URL; ?>/index.php?page=register" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo $_SESSION['old_input']['username'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?php echo $_SESSION['old_input']['nama_lengkap'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $_SESSION['old_input']['email'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                   value="<?php echo $_SESSION['old_input']['no_telp'] ?? ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"><?php echo $_SESSION['old_input']['alamat'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Register
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="<?php echo BASE_URL; ?>/index.php?page=login">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['old_input']);
require_once ROOT_PATH . '/src/views/layouts/footer.php'; 
?>
