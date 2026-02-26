<?php
require_once '../src/models/User.php';

class UserController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    public function index() {
        requireAdmin();
        
        $users = $this->userModel->getAllUsers();
        include '../src/views/admin/users.php';
    }
    
    public function create() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? '',
                'nama' => $_POST['nama'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? 'user'
            ];
            
            $errors = validateUser($data);
            
            if (empty($errors)) {
                if ($this->userModel->createUser($data)) {
                    setFlashMessage('User berhasil ditambahkan', 'success');
                    redirect('users');
                } else {
                    setFlashMessage('Gagal menambahkan user', 'error');
                }
            } else {
                setFlashMessage(implode('<br>', $errors), 'error');
            }
        }
        
        include '../src/views/admin/user-form.php';
    }
    
    public function edit() {
        requireAdmin();
        
        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            setFlashMessage('User tidak ditemukan', 'error');
            redirect('users');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? '',
                'nama' => $_POST['nama'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? 'user'
            ];
            
            if ($this->userModel->updateUser($id, $data)) {
                setFlashMessage('User berhasil diupdate', 'success');
                redirect('users');
            } else {
                setFlashMessage('Gagal mengupdate user', 'error');
            }
        }
        
        include '../src/views/admin/user-form.php';
    }
    
    public function delete() {
        requireAdmin();
        
        $id = $_GET['id'] ?? 0;
        
        if ($this->userModel->deleteUser($id)) {
            setFlashMessage('User berhasil dihapus', 'success');
        } else {
            setFlashMessage('Gagal menghapus user', 'error');
        }
        
        redirect('users');
    }
    
    public function dashboard() {
        requireLogin();
        
        include '../src/views/user/dashboard.php';
    }
}
?>
