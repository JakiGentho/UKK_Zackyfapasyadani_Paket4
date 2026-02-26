<?php
class LoanController {
    private $db;
    private $loanModel;
    private $bookModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->loanModel = new Loan($this->db);
        $this->bookModel = new Book($this->db);
    }

    public function index() {
        requireAdmin();
        
        $status = $_GET['status'] ?? null;
        $loans = $this->loanModel->getAll($status);
        
        require_once ROOT_PATH . '/src/views/admin/loans.php';
    }

    public function myLoans() {
        requireLogin();
        
        $user_id = getUserId();
        $loans = $this->loanModel->getAll(null, $user_id);
        
        require_once ROOT_PATH . '/src/views/user/my-loans.php';
    }

    public function request($book_id) {
        requireLogin();
        
        $user_id = getUserId();
        $book = $this->bookModel->getById($book_id);

        if (!$book) {
            setFlashMessage('error', 'Buku tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=user/catalog');
        }

        // Cek stok buku
        if ($book['tersedia'] <= 0) {
            setFlashMessage('error', 'Stok buku tidak tersedia');
            redirect(BASE_URL . '/index.php?page=user/catalog');
        }

        // Cek apakah user sudah meminjam buku ini
        if ($this->loanModel->hasActiveLoan($user_id, $book_id)) {
            setFlashMessage('error', 'Anda sudah memiliki peminjaman aktif untuk buku ini');
            redirect(BASE_URL . '/index.php?page=user/catalog');
        }

        // Cek maksimal buku yang dapat dipinjam
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'max_books_per_user'");
        $stmt->execute();
        $setting = $stmt->fetch();
        $max_books = $setting['setting_value'];

        $active_loans = $this->loanModel->countActiveLoans($user_id);

        if ($active_loans >= $max_books) {
            setFlashMessage('error', "Anda sudah mencapai batas maksimal peminjaman ($max_books buku)");
            redirect(BASE_URL . '/index.php?page=user/catalog');
        }

        // Get loan duration
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'loan_duration'");
        $stmt->execute();
        $setting = $stmt->fetch();
        $loan_duration = $setting['setting_value'];

        if ($this->loanModel->create($user_id, $book_id, $loan_duration)) {
            setFlashMessage('success', 'Permintaan peminjaman berhasil diajukan');
        } else {
            setFlashMessage('error', 'Gagal mengajukan peminjaman');
        }

        redirect(BASE_URL . '/index.php?page=user/my-loans');
    }

    public function approve($id) {
        requireAdmin();
        
        $loan = $this->loanModel->getById($id);

        if (!$loan) {
            setFlashMessage('error', 'Peminjaman tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/loans');
        }

        $book = $this->bookModel->getById($loan['book_id']);

        // Cek stok
        if ($book['tersedia'] <= 0) {
            setFlashMessage('error', 'Stok buku tidak tersedia');
            redirect(BASE_URL . '/index.php?page=admin/loans');
        }

        // Approve loan
        if ($this->loanModel->approve($id)) {
            // Update stok buku
            $new_stock = $book['tersedia'] - 1;
            $this->bookModel->updateStock($loan['book_id'], $new_stock);
            
            setFlashMessage('success', 'Peminjaman berhasil disetujui');
        } else {
            setFlashMessage('error', 'Gagal menyetujui peminjaman');
        }

        redirect(BASE_URL . '/index.php?page=admin/loans');
    }

    public function reject($id) {
        requireAdmin();
        
        $keterangan = $_POST['keterangan'] ?? '';

        if ($this->loanModel->reject($id, $keterangan)) {
            setFlashMessage('success', 'Peminjaman berhasil ditolak');
        } else {
            setFlashMessage('error', 'Gagal menolak peminjaman');
        }

        redirect(BASE_URL . '/index.php?page=admin/loans');
    }

    public function returnBook($id) {
        requireAdmin();
        
        $loan = $this->loanModel->getById($id);

        if (!$loan) {
            setFlashMessage('error', 'Peminjaman tidak ditemukan');
            redirect(BASE_URL . '/index.php?page=admin/loans');
        }

        // Get fine rate
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'fine_per_day'");
        $stmt->execute();
        $setting = $stmt->fetch();
        $fine_per_day = $setting['setting_value'];

        // Calculate fine
        $return_date = date('Y-m-d');
        $fine = calculateFine($loan['tanggal_jatuh_tempo'], $return_date, $fine_per_day);

        if ($this->loanModel->returnBook($id, $fine)) {
            // Update stok buku
            $book = $this->bookModel->getById($loan['book_id']);
            $new_stock = $book['tersedia'] + 1;
            $this->bookModel->updateStock($loan['book_id'], $new_stock);
            
            $message = 'Buku berhasil dikembalikan';
            if ($fine > 0) {
                $message .= ' dengan denda ' . formatRupiah($fine);
            }
            setFlashMessage('success', $message);
        } else {
            setFlashMessage('error', 'Gagal memproses pengembalian');
        }

        redirect(BASE_URL . '/index.php?page=admin/loans');
    }
}
?>
