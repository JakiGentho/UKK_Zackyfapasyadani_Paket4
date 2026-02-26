<?php
session_start();
include "koneksi.php";

// Cek apakah sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Cek apakah role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Administrator';

// Hitung statistik dari database
$query_buku = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_buku");
$total_buku = $query_buku ? mysqli_fetch_array($query_buku)['total'] : 0;

$query_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_anggota");
$total_anggota = $query_anggota ? mysqli_fetch_array($query_anggota)['total'] : 0;

$query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE status_kembali='belum'");
$total_pinjam = $query_pinjam ? mysqli_fetch_array($query_pinjam)['total'] : 0;

$query_total_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman");
$total_semua_pinjam = $query_total_pinjam ? mysqli_fetch_array($query_total_pinjam)['total'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>📚 Dashboard Admin Perpustakaan</h1>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>Selamat datang, <strong><?= htmlspecialchars($username) ?></strong>!</h2>
            <p>Role: <span style="background: #28a745; color: white; padding: 3px 10px; border-radius: 3px;">ADMIN</span></p>
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <h2><?= $total_buku ?></h2>
                <p>Total Buku</p>
            </div>
            <div class="stat-box">
                <h2><?= $total_anggota ?></h2>
                <p>Total Anggota</p>
            </div>
            <div class="stat-box">
                <h2><?= $total_pinjam ?></h2>
                <p>Sedang Dipinjam</p>
            </div>
            <div class="stat-box">
                <h2><?= $total_semua_pinjam ?></h2>
                <p>Total Transaksi</p>
            </div>
        </div>
        
        <div class="menu">
            <h3>Menu Admin</h3>
            <div class="menu-grid">
                <a href="admin/kelola_buku.php">📖 Kelola Buku</a>
                <a href="admin/kelola_anggota.php">👥 Kelola Anggota</a>
                <a href="admin/kelola_pinjam.php">📋 Kelola Peminjaman</a>
                <a href="admin/kelola_users.php">👤 Kelola Users</a>
                <a href="logout.php" class="logout">🚪 Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
