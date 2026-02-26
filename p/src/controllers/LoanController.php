<?php
require_once '../src/models/Loan.php';
require_once '../src/models/Book.php';

class LoanController {
    private $loanModel;
    private $bookModel;
    
    public function __construct($db) {
        $this->loanModel = new Loan($db);
        $this->bookModel = new Book($db);
    }
    
    public function index() {
        requireAdmin();
        
        $loans = $this->loanModel->getAllLoans();
        include '../src/views/admin/loans.php';
    }
    
    public function catalog() {
        requireLogin();
        
        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];
            $books = $this->bookModel->searchBooks($keyword);
        } else {
            $books = $this->bookModel->getAvailableBooks();
        }
        
        include '../src/views/user/catalog.php';
    }
    
    public function borrow() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $book_id = $_POST['book_id'] ?? 0;
            $book = $this->bookModel->getBookById($book_id);
            
            if (!$book || $book['stok'] <= 0) {
                setFlashMessage('Buku tidak tersedia', 'error');
                redirect('catalog');
                return;
            }
            
            $tanggal_pinjam = date('Y-m-d');
            $tanggal_kembali = date('Y-m-d', strtotime('+' . LAMA_PINJAM . ' days'));
            
            $data = [
                'user_id' => $_SESSION['user_id'],
                'book_id' => $book_id,
                'tanggal_pinjam' => $tanggal_pinjam,
                'tanggal_kembali' => $tanggal_kembali
            ];
            
            if ($this->loanModel->createLoan($data)) {
                $this->bookModel->updateStok($book_id, -1);
                setFlashMessage('Buku berhasil dipinjam. Harap dikembalikan sebelum ' . formatTanggal($tanggal_kembali), 'success');
            } else {
                setFlashMessage('Gagal meminjam buku', 'error');
            }
            
            redirect('catalog');
        }
    }
    
    public function myLoans() {
        requireLogin();
        
        $loans = $this->loanModel->getLoansByUserId($_SESSION['user_id']);
        include '../src/views/user/my-loans.php';
    }
    
    public function returnBook() {
        requireLogin();
        
        $id = $_GET['id'] ?? 0;
        $loan = $this->loanModel->getLoanById($id);
        
        if (!$loan || $loan['user_id'] != $_SESSION['user_id']) {
            setFlashMessage('Data peminjaman tidak ditemukan', 'error');
            redirect('my-loans');
            return;
        }
        
        $denda = hitungDenda($loan['tanggal_kembali']);
        
        if ($this->loanModel->returnBook($id, $denda)) {
            $this->bookModel->updateStok($loan['book_id'], 1);
            
            if ($denda > 0) {
                setFlashMessage('Buku berhasil dikembalikan. Denda keterlambatan: ' . formatRupiah($denda), 'warning');
            } else {
                setFlashMessage('Buku berhasil dikembalikan tepat waktu', 'success');
            }
        } else {
            setFlashMessage('Gagal mengembalikan buku', 'error');
        }
        
        redirect('my-loans');
    }
}
?>
