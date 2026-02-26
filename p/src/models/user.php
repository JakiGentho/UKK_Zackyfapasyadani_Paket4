<?php
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username AND password = MD5(:password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllUsers() {
        $query = "SELECT id, username, nama, email, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserById($id) {
        $query = "SELECT id, username, nama, email, role FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createUser($data) {
        $query = "INSERT INTO users (username, password, nama, email, role) 
                  VALUES (:username, MD5(:password), :nama, :email, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }
    
    public function updateUser($id, $data) {
        if (!empty($data['password'])) {
            $query = "UPDATE users SET username = :username, password = MD5(:password), 
                      nama = :nama, email = :email, role = :role WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $data['password']);
        } else {
            $query = "UPDATE users SET username = :username, nama = :nama, 
                      email = :email, role = :role WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
}
?>
