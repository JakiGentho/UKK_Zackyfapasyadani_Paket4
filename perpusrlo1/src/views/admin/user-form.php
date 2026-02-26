<?php 
$page_title = (isset($user) && $user) ? 'Edit User' : 'Tambah User';
$page_title .= ' - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';

$is_edit = isset($user) && $user;
$form_action = $is_edit ? 'admin/user/update&id=' . $user['id'] : 'admin/user/store';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-<?php echo $is_edit ? 'pencil' : 'person-plus'; ?>"></i>
                        <?php echo $is_edit ? 'Edit User' : 'Tambah User'; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/index.php?page=<?php echo $form_action; ?>" method="POST">
                        
                        <?php if (!$is_edit): ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo $_SESSION['old_input']['username'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?php echo $user['nama_lengkap'] ?? ($_SESSION['old_input']['nama_lengkap'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $user['email'] ?? ($_SESSION['old_input']['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                   value="<?php echo $user['no_telp'] ?? ($_SESSION['old_input']['no_telp'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $user['alamat'] ?? ($_SESSION['old_input']['alamat'] ?? ''); ?></textarea>
                        </div>
                        
                        <?php if ($is_edit): ?>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?php echo (isset($user) && $user['status'] == 'active') ? 'selected' : ''; ?>>
                                    Active
                                </option>
                                <option value="inactive" <?php echo (isset($user) && $user['status'] == 'inactive') ? 'selected' : ''; ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                            </button>
                            <a href="<?php echo BASE_URL; ?>/index.php?page=admin/users" class="btn btn-secondary">
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
