<?php
class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
    }

    public function showLogin() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        require_once ROOT_PATH . '/src/views/auth/login.php';
    }

    public function showRegister() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        require_once ROOT_PATH . '/src/views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=login');
        }

        $username = clean($_POST['username']);
        $password = $_POST['password'];

        $errors = [];

        if (empty($username)) {
            $errors[] = "Username harus diisi";
        }

        if (empty($password)) {
            $errors[] = "Password harus diisi";
        }

        if (empty($errors)) {
            $user = $this->userModel->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                setFlashMessage('success', 'Login berhasil! Selamat datang, ' . $user['nama_lengkap']);
                $this->redirectToDashboard();
            } else {
                $errors[] = "Username atau password salah, atau akun tidak aktif";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect(BASE_URL . '/index.php?page=login');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=register');
        }

        $data = [
            'username' => clean($_POST['username']),
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password'],
            'nama_lengkap' => clean($_POST['nama_lengkap']),
            'email' => clean($_POST['email']),
            'no_telp' => clean($_POST['no_telp']),
            'alamat' => clean($_POST['alamat']),
            'role' => 'user'
        ];

        $errors = [];

        // Validasi
        if (empty($data['username'])) {
            $errors[] = "Username harus diisi";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore (3-20 karakter)";
        } elseif ($this->userModel->usernameExists($data['username'])) {
            $errors[] = "Username sudah digunakan";
        }

        if (empty($data['password'])) {
            $errors[] = "Password harus diisi";
        } elseif (strlen($data['password']) < 6) {
            $errors[] = "Password minimal 6 karakter";
        }

        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Konfirmasi password tidak cocok";
        }

        if (empty($data['nama_lengkap'])) {
            $errors[] = "Nama lengkap harus diisi";
        }

        if (empty($data['email'])) {
            $errors[] = "Email harus diisi";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors[] = "Email sudah digunakan";
        }

        if (empty($errors)) {
            if ($this->userModel->register($data)) {
                setFlashMessage('success', 'Registrasi berhasil! Silakan login.');
                redirect(BASE_URL . '/index.php?page=login');
            } else {
                $errors[] = "Gagal melakukan registrasi";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect(BASE_URL . '/index.php?page=register');
    }

    public function logout() {
        session_destroy();
        redirect(BASE_URL . '/index.php?page=login');
    }

    private function redirectToDashboard() {
        if (isAdmin()) {
            redirect(BASE_URL . '/index.php?page=admin/dashboard');
        } else {
            redirect(BASE_URL . '/index.php?page=user/dashboard');
        }
    }
}
?>
