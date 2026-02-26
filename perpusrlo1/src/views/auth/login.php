<?php 
$page_title = 'Login - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="bi bi-book text-primary"></i> Login
                    </h2>
                    
                    <form action="<?php echo BASE_URL; ?>/index.php?page=login" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo $_SESSION['old_input']['username'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Belum punya akun? <a href="<?php echo BASE_URL; ?>/index.php?page=register">Daftar sekarang</a></p>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <strong>Demo Account:</strong><br>
                            Admin - Username: <code>admin</code> | Password: <code>password</code>
                        </small>
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
