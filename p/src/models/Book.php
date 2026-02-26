<?php
class Book {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllBooks() {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM books b 
                  LEFT JOIN categories c ON b.kategori_id = c.id 
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAvailableBooks() {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM books b 
                  LEFT JOIN categories c ON b.kategori_id = c.id 
                  WHERE b.stok > 0 
                  ORDER BY b.judul ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getBookById($id) {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM books b 
                  LEFT JOIN categories c ON b.kategori_id = c.id 
                  WHERE b.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createBook($data) {
        $query = "INSERT INTO books (kode_buku, judul, pengarang, penerbit, tahun_terbit, stok, kategori_id, deskripsi) 
                  VALUES (:kode_buku, :judul, :pengarang, :penerbit, :tahun_terbit, :stok, :kategori_id, :deskripsi)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_buku', $data['kode_buku']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        
        // Handle tahun_terbit - set NULL jika kosong atau tidak valid
        $tahun_terbit = !empty($data['tahun_terbit']) && is_numeric($data['tahun_terbit']) ? $data['tahun_terbit'] : null;
        $stmt->bindParam(':tahun_terbit', $tahun_terbit);
        
        $stmt->bindParam(':stok', $data['stok']);
        
        // Handle kategori_id - set NULL jika kosong
        $kategori_id = !empty($data['kategori_id']) ? $data['kategori_id'] : null;
        $stmt->bindParam(':kategori_id', $kategori_id);
        
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        
        return $stmt->execute();
    }
    
    public function updateBook($id, $data) {
        $query = "UPDATE books SET kode_buku = :kode_buku, judul = :judul, pengarang = :pengarang, 
                  penerbit = :penerbit, tahun_terbit = :tahun_terbit, stok = :stok, 
                  kategori_id = :kategori_id, deskripsi = :deskripsi WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':kode_buku', $data['kode_buku']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        
        // Handle tahun_terbit - set NULL jika kosong atau tidak valid
        $tahun_terbit = !empty($data['tahun_terbit']) && is_numeric($data['tahun_terbit']) ? $data['tahun_terbit'] : null;
        $stmt->bindParam(':tahun_terbit', $tahun_terbit);
        
        $stmt->bindParam(':stok', $data['stok']);
        
        // Handle kategori_id - set NULL jika kosong
        $kategori_id = !empty($data['kategori_id']) ? $data['kategori_id'] : null;
        $stmt->bindParam(':kategori_id', $kategori_id);
        
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        
        return $stmt->execute();
    }
    
    public function deleteBook($id) {
        $query = "DELETE FROM books WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function updateStok($id, $perubahan) {
        $query = "UPDATE books SET stok = stok + :perubahan WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':perubahan', $perubahan);
        
        return $stmt->execute();
    }
    
    public function countBooks() {
        $query = "SELECT COUNT(*) as total FROM books";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
    
    public function searchBooks($keyword) {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM books b 
                  LEFT JOIN categories c ON b.kategori_id = c.id 
                  WHERE b.judul LIKE :keyword OR b.pengarang LIKE :keyword OR b.kode_buku LIKE :keyword
                  ORDER BY b.judul ASC";
        $stmt = $this->conn->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
