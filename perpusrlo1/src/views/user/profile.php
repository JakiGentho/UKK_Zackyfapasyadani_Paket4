<?php 
$page_title = 'Profil Saya - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="bi bi-person"></i> Profil Saya</h2>
    
    <div class="row">
        <!-- Update Profile -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/index.php?page=user/profile/update" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" disabled>
                            <small class="text-muted">Username tidak dapat diubah</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?php echo $user['nama_lengkap']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $user['email']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                   value="<?php echo $user['no_telp']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $user['alamat']; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-key"></i> Ganti Password</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/index.php?page=user/password/change" method="POST">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Password Lama *</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru *</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Ganti Password
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Account Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Terdaftar:</strong></td>
                            <td><?php echo formatDateTime($user['created_at']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td><span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
