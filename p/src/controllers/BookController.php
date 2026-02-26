<?php
require_once '../src/models/Book.php';
require_once '../src/models/Category.php';

class BookController {
    private $bookModel;
    private $categoryModel;
    
    public function __construct($db) {
        $this->bookModel = new Book($db);
        $this->categoryModel = new Category($db);
    }
    
    public function index() {
        requireAdmin();
        
        $books = $this->bookModel->getAllBooks();
        include '../src/views/admin/books.php';
    }
    
    public function create() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'kode_buku' => $_POST['kode_buku'] ?? '',
                'judul' => $_POST['judul'] ?? '',
                'pengarang' => $_POST['pengarang'] ?? '',
                'penerbit' => $_POST['penerbit'] ?? '',
                'tahun_terbit' => $_POST['tahun_terbit'] ?? '',
                'stok' => $_POST['stok'] ?? 0,
                'kategori_id' => $_POST['kategori_id'] ?? null,
                'deskripsi' => $_POST['deskripsi'] ?? ''
            ];
            
            $errors = validateBook($data);
            
            if (empty($errors)) {
                if ($this->bookModel->createBook($data)) {
                    setFlashMessage('Buku berhasil ditambahkan', 'success');
                    redirect('books');
                } else {
                    setFlashMessage('Gagal menambahkan buku', 'error');
                }
            } else {
                setFlashMessage(implode('<br>', $errors), 'error');
            }
        }
        
        $categories = $this->categoryModel->getAllCategories();
        include '../src/views/admin/book-form.php';
    }
    
    public function edit() {
        requireAdmin();
        
        $id = $_GET['id'] ?? 0;
        $book = $this->bookModel->getBookById($id);
        
        if (!$book) {
            setFlashMessage('Buku tidak ditemukan', 'error');
            redirect('books');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'kode_buku' => $_POST['kode_buku'] ?? '',
                'judul' => $_POST['judul'] ?? '',
                'pengarang' => $_POST['pengarang'] ?? '',
                'penerbit' => $_POST['penerbit'] ?? '',
                'tahun_terbit' => $_POST['tahun_terbit'] ?? '',
                'stok' => $_POST['stok'] ?? 0,
                'kategori_id' => $_POST['kategori_id'] ?? null,
                'deskripsi' => $_POST['deskripsi'] ?? ''
            ];
            
            $errors = validateBook($data);
            
            if (empty($errors)) {
                if ($this->bookModel->updateBook($id, $data)) {
                    setFlashMessage('Buku berhasil diupdate', 'success');
                    redirect('books');
                } else {
                    setFlashMessage('Gagal mengupdate buku', 'error');
                }
            } else {
                setFlashMessage(implode('<br>', $errors), 'error');
            }
        }
        
        $categories = $this->categoryModel->getAllCategories();
        include '../src/views/admin/book-form.php';
    }
    
    public function delete() {
        requireAdmin();
        
        $id = $_GET['id'] ?? 0;
        
        if ($this->bookModel->deleteBook($id)) {
            setFlashMessage('Buku berhasil dihapus', 'success');
        } else {
            setFlashMessage('Gagal menghapus buku', 'error');
        }
        
        redirect('books');
    }
}
?>
