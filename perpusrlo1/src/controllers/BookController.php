<?php
class BookController {
    private $db;
    private $bookModel;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->bookModel = new Book($this->db);
        $this->categoryModel = new Category($this->db);
    }

    public function index() {
        requireAdmin();
        
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        
        $books = $this->bookModel->getAll($search, $category_id);
        $categories = $this->categoryModel->getAll();
        
        require_once ROOT_PATH . '/src/views/admin/books.php';
    }

    public function create() {
        requireAdmin();
        
        $categories = $this->categoryModel->getAll();
        $book = null;
        
        require_once ROOT_PATH . '/src/views/admin/book-form.php';
    }

    public function store() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=admin/books');
        }

        $data = [
            'isbn' => clean($_POST['isbn']),
            'judul' => clean($_POST['judul']),
            'pengarang' => clean($_POST['pengarang']),
            'penerbit' => clean($_POST['penerbit']),
            'tahun_terbit' => clean($_POST['tahun_terbit']),
            'category_id' => clean($_POST['category_id']),
            'stok' => clean($_POST['stok']),
            'lokasi_rak' => clean($_POST['lokasi_rak']),
            'deskripsi' => clean($_POST['deskripsi']),
            'cover_image' => ''
        ];

        $errors = [];

        // Validasi
        if (empty($data['judul'])) {
            $errors[] = "Judul buku harus diisi";
        }

        if (empty($data['pengarang'])) {
            $errors[] = "Pengarang harus diisi";
        }

        if (!empty($data['isbn']) && $this->bookModel->isbnExists($data['isbn'])) {
            $errors[] = "ISBN sudah terdaftar";
        }

        // Upload cover image
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = uploadFile($_FILES['cover_image'], COVER_PATH);
            
            if ($upload_result['success']) {
                $data['cover_image'] = $upload_result['filename'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }

        if (empty($errors)) {
            if ($this->bookModel->create($data)) {
                setFlashMessage('success', 'Buku berhasil ditambahkan');
                redirect(BASE_URL . '/index.php?page=admin/books');
            } else {
                $errors[] = "Gagal menambahkan buku";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect(BASE_URL . '/index.php?page=admin/book/create');
    }

    public function edit($id) {
        requireAdmin();
        
        $book = $this->bookModel->getById($id);
        
        if (!$book) {
            setFlashMessage('error', 'Buku tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/books');
        }
        
        $categories = $this->categoryModel->getAll();
        
        require_once ROOT_PATH . '/src/views/admin/book-form.php';
    }

    public function update($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=admin/books');
        }

        $book = $this->bookModel->getById($id);
        
        if (!$book) {
            setFlashMessage('error', 'Buku tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/books');
        }

        $data = [
            'isbn' => clean($_POST['isbn']),
            'judul' => clean($_POST['judul']),
            'pengarang' => clean($_POST['pengarang']),
            'penerbit' => clean($_POST['penerbit']),
            'tahun_terbit' => clean($_POST['tahun_terbit']),
            'category_id' => clean($_POST['category_id']),
            'stok' => clean($_POST['stok']),
            'lokasi_rak' => clean($_POST['lokasi_rak']),
            'deskripsi' => clean($_POST['deskripsi']),
            'cover_image' => ''
        ];

        $errors = [];

        // Validasi
        if (empty($data['judul'])) {
            $errors[] = "Judul buku harus diisi";
        }

        if (empty($data['pengarang'])) {
            $errors[] = "Pengarang harus diisi";
        }

        if (!empty($data['isbn']) && $this->bookModel->isbnExists($data['isbn'], $id)) {
            $errors[] = "ISBN sudah digunakan oleh buku lain";
        }

        // Upload cover image baru
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = uploadFile($_FILES['cover_image'], COVER_PATH);
            
            if ($upload_result['success']) {
                // Hapus cover lama
                if (!empty($book['cover_image'])) {
                    deleteFile(COVER_PATH . $book['cover_image']);
                }
                $data['cover_image'] = $upload_result['filename'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }

        if (empty($errors)) {
            if ($this->bookModel->update($id, $data)) {
                setFlashMessage('success', 'Buku berhasil diperbarui');
                redirect(BASE_URL . '/index.php?page=admin/books');
            } else {
                $errors[] = "Gagal memperbarui buku";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect(BASE_URL . '/index.php?page=admin/book/edit&id=' . $id);
    }

    public function delete($id) {
        requireAdmin();
        
        $book = $this->bookModel->getById($id);
        
        if (!$book) {
            setFlashMessage('error', 'Buku tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/books');
        }

        // Hapus cover image
        if (!empty($book['cover_image'])) {
            deleteFile(COVER_PATH . $book['cover_image']);
        }

        if ($this->bookModel->delete($id)) {
            setFlashMessage('success', 'Buku berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus buku');
        }

        redirect(BASE_URL . '/index.php?page=admin/books');
    }
}
?>
