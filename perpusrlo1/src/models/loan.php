<?php
class Loan {
    private $conn;
    private $table = 'loans';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($user_id, $book_id, $loan_duration) {
        $query = "INSERT INTO {$this->table} 
                  (user_id, book_id, tanggal_pinjam, tanggal_jatuh_tempo, status) 
                  VALUES (:user_id, :book_id, :tanggal_pinjam, :tanggal_jatuh_tempo, 'pending')";
        
        $stmt = $this->conn->prepare($query);
        
        $tanggal_pinjam = date('Y-m-d');
        $tanggal_jatuh_tempo = date('Y-m-d', strtotime("+{$loan_duration} days"));
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->bindParam(':tanggal_pinjam', $tanggal_pinjam);
        $stmt->bindParam(':tanggal_jatuh_tempo', $tanggal_jatuh_tempo);
        
        return $stmt->execute();
    }

    public function getAll($status = null, $user_id = null) {
        $query = "SELECT l.*, u.nama_lengkap, u.username, b.judul, b.pengarang 
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.id
                  JOIN books b ON l.book_id = b.id
                  WHERE 1=1";
        
        if ($status) {
            $query .= " AND l.status = :status";
        }
        
        if ($user_id) {
            $query .= " AND l.user_id = :user_id";
        }
        
        $query .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT l.*, u.nama_lengkap, u.username, u.email, 
                  b.judul, b.pengarang, b.isbn, c.nama_kategori
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.id
                  JOIN books b ON l.book_id = b.id
                  LEFT JOIN categories c ON b.category_id = c.id
                  WHERE l.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function approve($id) {
        $query = "UPDATE {$this->table} SET status = 'approved' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function reject($id, $keterangan = '') {
        $query = "UPDATE {$this->table} SET status = 'rejected', keterangan = :keterangan WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':keterangan', $keterangan);
        return $stmt->execute();
    }

    public function returnBook($id, $fine = 0) {
        $query = "UPDATE {$this->table} SET 
                  status = 'returned', 
                  tanggal_kembali = :tanggal_kembali,
                  denda = :denda 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $tanggal_kembali = date('Y-m-d');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':tanggal_kembali', $tanggal_kembali);
        $stmt->bindParam(':denda', $fine);
        
        return $stmt->execute();
    }

    public function countActiveLoans($user_id = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE status IN ('pending', 'approved')";
        
        if ($user_id) {
            $query .= " AND user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getTotalFines() {
        $query = "SELECT SUM(denda) as total FROM {$this->table} WHERE status = 'returned'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function hasActiveLoan($user_id, $book_id) {
        $query = "SELECT id FROM {$this->table} 
                  WHERE user_id = :user_id AND book_id = :book_id 
                  AND status IN ('pending', 'approved') LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>
