<?php
class Book {
    private $conn;
    private $table = 'books';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (isbn, judul, pengarang, penerbit, tahun_terbit, category_id, 
                   stok, tersedia, lokasi_rak, cover_image, deskripsi) 
                  VALUES (:isbn, :judul, :pengarang, :penerbit, :tahun_terbit, 
                          :category_id, :stok, :tersedia, :lokasi_rak, :cover_image, :deskripsi)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':tersedia', $data['stok']);
        $stmt->bindParam(':lokasi_rak', $data['lokasi_rak']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        
        return $stmt->execute();
    }

    public function getAll($search = '', $category_id = null) {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM {$this->table} b 
                  LEFT JOIN categories c ON b.category_id = c.id 
                  WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (b.judul LIKE :search OR b.pengarang LIKE :search OR b.isbn LIKE :search)";
        }
        
        if ($category_id) {
            $query .= " AND b.category_id = :category_id";
        }
        
        $query .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $search_param = "%$search%";
            $stmt->bindParam(':search', $search_param);
        }
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT b.*, c.nama_kategori 
                  FROM {$this->table} b 
                  LEFT JOIN categories c ON b.category_id = c.id 
                  WHERE b.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  isbn = :isbn,
                  judul = :judul,
                  pengarang = :pengarang,
                  penerbit = :penerbit,
                  tahun_terbit = :tahun_terbit,
                  category_id = :category_id,
                  stok = :stok,
                  lokasi_rak = :lokasi_rak,
                  deskripsi = :deskripsi";
        
        if (!empty($data['cover_image'])) {
            $query .= ", cover_image = :cover_image";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':lokasi_rak', $data['lokasi_rak']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        
        if (!empty($data['cover_image'])) {
            $stmt->bindParam(':cover_image', $data['cover_image']);
        }
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateStock($id, $tersedia) {
        $query = "UPDATE {$this->table} SET tersedia = :tersedia WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':tersedia', $tersedia);
        return $stmt->execute();
    }

    public function countBooks() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function isbnExists($isbn, $exclude_id = null) {
        $query = "SELECT id FROM {$this->table} WHERE isbn = :isbn";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':isbn', $isbn);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>
