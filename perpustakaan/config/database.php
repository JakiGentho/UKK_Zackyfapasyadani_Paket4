<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'perpustakaan');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('BASE_URL',   'http://localhost/perpustakaan/');
define('FINE_PER_DAY', 1000);   // Rp 1.000/hari
define('LOAN_DAYS',    7);       // durasi pinjam default

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER, DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='font-family:Arial;color:red;padding:30px'>
         <h3>❌ Koneksi Database Gagal</h3>
         <p>".$e->getMessage()."</p>
         <p>Pastikan Laragon berjalan dan database <b>perpustakaan</b> sudah diimpor.</p>
         </div>");
}
