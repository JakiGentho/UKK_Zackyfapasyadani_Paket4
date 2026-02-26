<?php
$title = isset($user) ? "Edit User" : "Tambah User";
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
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo escape($user['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password <?php echo isset($user) ? '(Kosongkan jika tidak diubah)' : ''; ?></label>
                <input type="password" id="password" name="password" 
                       <?php echo !isset($user) ? 'required' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" 
                       value="<?php echo escape($user['nama'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo escape($user['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" <?php echo (isset($user) && $user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo (isset($user) && $user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="?page=users" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
