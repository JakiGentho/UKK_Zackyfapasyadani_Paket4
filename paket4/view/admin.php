<?php
session_start();
include "../koneksi.php";

// Cek apakah sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Cek apakah role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Administrator';

// Hitung statistik dari database dengan error handling
$total_buku = 0;
$total_anggota = 0;
$total_pinjam = 0;
$total_semua_pinjam = 0;

// Cek tabel buku
$query_buku = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_buku");
if ($query_buku) {
    $total_buku = mysqli_fetch_array($query_buku)['total'];
}

// Cek tabel anggota
$query_anggota = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_anggota");
if ($query_anggota) {
    $total_anggota = mysqli_fetch_array($query_anggota)['total'];
}

// Cek tabel peminjaman
$query_pinjam = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE status_kembali='belum'");
if ($query_pinjam) {
    $total_pinjam = mysqli_fetch_array($query_pinjam)['total'];
}

$query_total_pinjam = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman");
if ($query_total_pinjam) {
    $total_semua_pinjam = mysqli_fetch_array($query_total_pinjam)['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>📚 Dashboard Admin Perpustakaan</h1>
                <div class="user-info">
                    <div class="user-dropdown">
                        <div class="user-button">
                            <div class="user-avatar">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span class="username"><?= htmlspecialchars($username) ?></span>
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <div class="dropdown-menu">
                            <a href="user.php?page=profil" class="dropdown-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="../logout.php" class="dropdown-item logout-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📖</div>
                    <div class="stat-info">
                        <h3><?= $total_buku ?></h3>
                        <p>Total Buku</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?= $total_anggota ?></h3>
                        <p>Total Anggota</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <h3><?= $total_pinjam ?></h3>
                        <p>Sedang Dipinjam</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?= $total_semua_pinjam ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                </div>
            </div>
            
            <!-- Menu Grid -->
            <div class="menu-section">
                <h2>Menu Administrasi</h2>
                <div class="menu-grid">
                    <a href="user.php?page=buku" class="menu-card">
                        <div class="menu-icon">📖</div>
                        <div class="menu-text">
                            <h3>Kelola Buku</h3>
                            <p>Tambah, edit, hapus buku</p>
                        </div>
                    </a>
                    <a href="user.php?page=anggota" class="menu-card">
                        <div class="menu-icon">👥</div>
                        <div class="menu-text">
                            <h3>Kelola Anggota</h3>
                            <p>Manajemen data anggota</p>
                        </div>
                    </a>
                    <a href="user.php?page=pinjam" class="menu-card">
                        <div class="menu-icon">📋</div>
                        <div class="menu-text">
                            <h3>Kelola Peminjaman</h3>
                            <p>Transaksi peminjaman</p>
                        </div>
                    </a>
                    <a href="user.php?page=users" class="menu-card">
                        <div class="menu-icon">👤</div>
                        <div class="menu-text">
                            <h3>Kelola Users</h3>
                            <p>Manajemen pengguna</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
