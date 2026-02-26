<?php
class Loan {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllLoans() {
        $query = "SELECT l.*, u.nama as nama_user, b.judul as judul_buku, b.kode_buku 
                  FROM loans l 
                  JOIN users u ON l.user_id = u.id 
                  JOIN books b ON l.book_id = b.id 
                  ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLoansByUserId($user_id) {
        $query = "SELECT l.*, b.judul, b.pengarang, b.kode_buku 
                  FROM loans l 
                  JOIN books b ON l.book_id = b.id 
                  WHERE l.user_id = :user_id 
                  ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLoanById($id) {
        $query = "SELECT l.*, u.nama as nama_user, b.judul as judul_buku, b.id as book_id 
                  FROM loans l 
                  JOIN users u ON l.user_id = u.id 
                  JOIN books b ON l.book_id = b.id 
                  WHERE l.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createLoan($data) {
        $query = "INSERT INTO loans (user_id, book_id, tanggal_pinjam, tanggal_kembali, status) 
                  VALUES (:user_id, :book_id, :tanggal_pinjam, :tanggal_kembali, 'dipinjam')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':tanggal_pinjam', $data['tanggal_pinjam']);
        $stmt->bindParam(':tanggal_kembali', $data['tanggal_kembali']);
        
        return $stmt->execute();
    }
    
    public function returnBook($id, $denda = 0) {
        $query = "UPDATE loans SET tanggal_dikembalikan = CURDATE(), status = 'dikembalikan', denda = :denda 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':denda', $denda);
        
        return $stmt->execute();
    }
    
    public function countActiveLoans() {
        $query = "SELECT COUNT(*) as total FROM loans WHERE status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
    
    public function countOverdueLoans() {
        $query = "SELECT COUNT(*) as total FROM loans 
                  WHERE status = 'dipinjam' AND tanggal_kembali < CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
}
?>
