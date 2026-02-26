<?php
class AdminController {
    private $db;
    private $userModel;
    private $bookModel;
    private $loanModel;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
        $this->bookModel = new Book($this->db);
        $this->loanModel = new Loan($this->db);
        $this->categoryModel = new Category($this->db);
    }

    public function dashboard() {
        requireAdmin();
        
        // Get statistics
        $total_books = $this->bookModel->countBooks();
        $total_users = $this->userModel->countUsers('user');
        $active_loans = $this->loanModel->countActiveLoans();
        $total_fines = $this->loanModel->getTotalFines();
        
        // Get recent loans
        $recent_loans = $this->loanModel->getAll(null, null);
        $recent_loans = array_slice($recent_loans, 0, 5);
        
        require_once ROOT_PATH . '/src/views/admin/dashboard.php';
    }

    public function users() {
        requireAdmin();
        
        $users = $this->userModel->getAll('user');
        
        require_once ROOT_PATH . '/src/views/admin/users.php';
    }

    public function createUser() {
        requireAdmin();
        
        $user = null;
        require_once ROOT_PATH . '/src/views/admin/user-form.php';
    }

    public function storeUser() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=admin/users');
        }

        $data = [
            'username' => clean($_POST['username']),
            'password' => $_POST['password'],
            'nama_lengkap' => clean($_POST['nama_lengkap']),
            'email' => clean($_POST['email']),
            'no_telp' => clean($_POST['no_telp']),
            'alamat' => clean($_POST['alamat']),
            'role' => 'user'
        ];

        $errors = [];

        if (empty($data['username'])) {
            $errors[] = "Username harus diisi";
        } elseif ($this->userModel->usernameExists($data['username'])) {
            $errors[] = "Username sudah digunakan";
        }

        if (empty($data['password'])) {
            $errors[] = "Password harus diisi";
        }

        if (empty($data['nama_lengkap'])) {
            $errors[] = "Nama lengkap harus diisi";
        }

        if (empty($data['email'])) {
            $errors[] = "Email harus diisi";
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors[] = "Email sudah digunakan";
        }

        if (empty($errors)) {
            if ($this->userModel->register($data)) {
                setFlashMessage('success', 'User berhasil ditambahkan');
                redirect(BASE_URL . '/index.php?page=admin/users');
            } else {
                $errors[] = "Gagal menambahkan user";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect(BASE_URL . '/index.php?page=admin/user/create');
    }

    public function editUser($id) {
        requireAdmin();
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            setFlashMessage('error', 'User tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/users');
        }
        
        require_once ROOT_PATH . '/src/views/admin/user-form.php';
    }

    public function updateUser($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=admin/users');
        }

        $data = [
            'nama_lengkap' => clean($_POST['nama_lengkap']),
            'email' => clean($_POST['email']),
            'no_telp' => clean($_POST['no_telp']),
            'alamat' => clean($_POST['alamat']),
            'status' => clean($_POST['status'])
        ];

        $errors = [];

        if (empty($data['nama_lengkap'])) {
            $errors[] = "Nama lengkap harus diisi";
        }

        if (empty($data['email'])) {
            $errors[] = "Email harus diisi";
        } elseif ($this->userModel->emailExists($data['email'], $id)) {
            $errors[] = "Email sudah digunakan";
        }

        if (empty($errors)) {
            if ($this->userModel->update($id, $data)) {
                setFlashMessage('success', 'User berhasil diperbarui');
                redirect(BASE_URL . '/index.php?page=admin/users');
            } else {
                $errors[] = "Gagal memperbarui user";
            }
        }

        $_SESSION['errors'] = $errors;
        redirect(BASE_URL . '/index.php?page=admin/user/edit&id=' . $id);
    }

    public function deleteUser($id) {
        requireAdmin();
        
        if ($this->userModel->delete($id)) {
            setFlashMessage('success', 'User berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus user');
        }

        redirect(BASE_URL . '/index.php?page=admin/users');
    }

    public function categories() {
        requireAdmin();
        
        $categories = $this->categoryModel->getAll();
        
        require_once ROOT_PATH . '/src/views/admin/categories.php';
    }

    public function storeCategory() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=admin/categories');
        }

        $data = [
            'nama_kategori' => clean($_POST['nama_kategori']),
            'deskripsi' => clean($_POST['deskripsi'])
        ];

        if ($this->categoryModel->create($data)) {
            setFlashMessage('success', 'Kategori berhasil ditambahkan');
        } else {
            setFlashMessage('error', 'Gagal menambahkan kategori');
        }

        redirect(BASE_URL . '/index.php?page=admin/categories');
    }

    public function deleteCategory($id) {
        requireAdmin();
        
        if ($this->categoryModel->delete($id)) {
            setFlashMessage('success', 'Kategori berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus kategori');
        }

        redirect(BASE_URL . '/index.php?page=admin/categories');
    }
}
?>
