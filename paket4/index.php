<?php
session_start();
include "koneksi.php";

// Jika sudah login, arahkan sesuai role
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: view/admin.php");
    } else {
        header("Location: view/dashboard.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Query ke tabel users
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);
        
        // Cek password
        if ($password === $data['password']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            
            // Redirect berdasarkan role
            if ($data['role'] === 'admin') {
                header("Location: view/admin.php");
            } else {
                header("Location: view/dashboard.php");
            }
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="split-container">
        <!-- Bagian Kiri: Sambutan -->
        <div class="left-side">
            <div class="welcome-content">
                <h1>Selamat Datang</h1>
                <p>di Sistem Informasi Perpustakaan</p>
                <div class="welcome-icon">📚</div>
                <div class="creator-info">
                    <p class="creator-label">Dibuat oleh:</p>
                    <p class="creator-name">Firdaus Rilo</p>
                    <p class="creator-class">XII RPL</p>
                </div>
            </div>
        </div>
        
        <!-- Bagian Kanan: Form Login -->
        <div class="right-side">
            <div class="login-box">
                <h2>Login</h2>
                <p class="login-subtitle">Masuk ke akun Anda</p>
                
                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eye-off-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">Masuk</button>
                </form>
                
                <div class="login-info">
                    <p><strong>Akun Demo:</strong></p>
                    <p>Admin: <code>admin</code> / <code>admin123</code></p>
                    <p>User: <code>user1</code> / <code>user1</code></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>
</html>
