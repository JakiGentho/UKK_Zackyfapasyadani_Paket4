<?php
class UserController {
    private $db;
    private $bookModel;
    private $categoryModel;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->bookModel = new Book($this->db);
        $this->categoryModel = new Category($this->db);
        $this->userModel = new User($this->db);
    }

    public function dashboard() {
        requireLogin();
        
        $user_id = getUserId();
        $loanModel = new Loan($this->db);
        
        $active_loans = $loanModel->countActiveLoans($user_id);
        $all_loans = $loanModel->getAll(null, $user_id);
        $recent_books = $this->bookModel->getAll('', null);
        $recent_books = array_slice($recent_books, 0, 6);
        
        require_once ROOT_PATH . '/src/views/user/dashboard.php';
    }

    public function catalog() {
        requireLogin();
        
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        
        $books = $this->bookModel->getAll($search, $category_id);
        $categories = $this->categoryModel->getAll();
        
        require_once ROOT_PATH . '/src/views/user/catalog.php';
    }

    public function profile() {
        requireLogin();
        
        $user_id = getUserId();
        $user = $this->userModel->getById($user_id);
        
        require_once ROOT_PATH . '/src/views/user/profile.php';
    }

    public function updateProfile() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=user/profile');
        }

        $user_id = getUserId();
        
        $data = [
            'nama_lengkap' => clean($_POST['nama_lengkap']),
            'email' => clean($_POST['email']),
            'no_telp' => clean($_POST['no_telp']),
            'alamat' => clean($_POST['alamat']),
            'status' => 'active'
        ];

        $errors = [];

        if (empty($data['nama_lengkap'])) {
            $errors[] = "Nama lengkap harus diisi";
        }

        if (empty($data['email'])) {
            $errors[] = "Email harus diisi";
        } elseif ($this->userModel->emailExists($data['email'], $user_id)) {
            $errors[] = "Email sudah digunakan";
        }

        if (empty($errors)) {
            if ($this->userModel->update($user_id, $data)) {
                $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
                $_SESSION['email'] = $data['email'];
                setFlashMessage('success', 'Profil berhasil diperbarui');
            } else {
                $errors[] = "Gagal memperbarui profil";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }

        redirect(BASE_URL . '/index.php?page=user/profile');
    }

    public function changePassword() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=user/profile');
        }

        $user_id = getUserId();
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $errors = [];

        // Verify old password
        $user = $this->userModel->getById($user_id);
        if (!password_verify($old_password, $user['password'])) {
            $errors[] = "Password lama tidak sesuai";
        }

        if (strlen($new_password) < 6) {
            $errors[] = "Password baru minimal 6 karakter";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Konfirmasi password tidak cocok";
        }

        if (empty($errors)) {
            if ($this->userModel->updatePassword($user_id, $new_password)) {
                setFlashMessage('success', 'Password berhasil diubah');
            } else {
                $errors[] = "Gagal mengubah password";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }

        redirect(BASE_URL . '/index.php?page=user/profile');
    }
}
?>
