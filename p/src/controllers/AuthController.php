<?php
require_once '../src/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    public function showLogin() {
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('admin-dashboard');
            } else {
                redirect('user-dashboard');
            }
            return;
        }
        
        include '../src/views/auth/login.php';
    }
    
    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $errors = validateLogin($username, $password);
        
        if (empty($errors)) {
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    redirect('admin-dashboard');
                } else {
                    redirect('user-dashboard');
                }
            } else {
                setFlashMessage('Username atau password salah', 'error');
                redirect('login');
            }
        } else {
            setFlashMessage(implode('<br>', $errors), 'error');
            redirect('login');
        }
    }
    
    public function logout() {
        session_destroy();
        redirect('login');
    }
}
?>
