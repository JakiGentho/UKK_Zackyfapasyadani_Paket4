<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        $query = "INSERT INTO {$this->table} 
                  (username, password, nama_lengkap, email, no_telp, alamat, role) 
                  VALUES (:username, :password, :nama_lengkap, :email, :no_telp, :alamat, :role)";
        
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telp', $data['no_telp']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE username = :username AND status = 'active' LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAll($role = null) {
        $query = "SELECT * FROM {$this->table}";
        if ($role) {
            $query .= " WHERE role = :role";
        }
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  nama_lengkap = :nama_lengkap,
                  email = :email,
                  no_telp = :no_telp,
                  alamat = :alamat,
                  status = :status
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telp', $data['no_telp']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }

    public function updatePassword($id, $new_password) {
        $query = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function usernameExists($username, $exclude_id = null) {
        $query = "SELECT id FROM {$this->table} WHERE username = :username";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function countUsers($role = 'user') {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>
