<?php
$title = "Login - " . SITE_NAME;
include '../src/views/layouts/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h2><?php echo SITE_NAME; ?></h2>
        
        <?php 
        $flash = getFlashMessage();
        if ($flash): 
        ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'info'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>
        
        <form action="?page=login" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="login-note">
            Default: admin / admin123
        </p>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
