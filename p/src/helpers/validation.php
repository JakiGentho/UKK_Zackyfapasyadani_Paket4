<?php
function validateLogin($username, $password) {
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username harus diisi';
    }
    
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    }
    
    return $errors;
}

function validateBook($data) {
    $errors = [];
    
    if (empty($data['kode_buku'])) {
        $errors[] = 'Kode buku harus diisi';
    }
    
    if (empty($data['judul'])) {
        $errors[] = 'Judul harus diisi';
    }
    
    if (empty($data['pengarang'])) {
        $errors[] = 'Pengarang harus diisi';
    }
    
    if (!is_numeric($data['stok']) || $data['stok'] < 0) {
        $errors[] = 'Stok harus berupa angka positif';
    }
    
    // Validasi tahun terbit (opsional, tapi jika diisi harus valid)
    if (!empty($data['tahun_terbit'])) {
        if (!is_numeric($data['tahun_terbit'])) {
            $errors[] = 'Tahun terbit harus berupa angka';
        } elseif ($data['tahun_terbit'] < 1901 || $data['tahun_terbit'] > 2155) {
            $errors[] = 'Tahun terbit harus antara 1901 - 2155';
        }
    }
    
    return $errors;
}

function validateUser($data) {
    $errors = [];
    
    if (empty($data['username']) || strlen($data['username']) < 4) {
        $errors[] = 'Username minimal 4 karakter';
    }
    
    if (empty($data['password']) || strlen($data['password']) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }
    
    if (empty($data['nama'])) {
        $errors[] = 'Nama harus diisi';
    }
    
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    
    return $errors;
}
?>
