<?php
// Fungsi untuk escape HTML - handle null dan empty values
function escape($string) {
    if ($string === null || $string === '') {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fungsi redirect
function redirect($page) {
    header('Location: ' . BASE_URL . 'index.php?page=' . $page);
    exit();
}

// Fungsi format tanggal - handle null dan invalid date
function formatTanggal($date) {
    // Handle null, empty, atau invalid date
    if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($date);
    
    // Jika strtotime gagal, return '-'
    if ($timestamp === false) {
        return '-';
    }
    
    $tanggal = date('d', $timestamp);
    $bulanAngka = (int)date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $tanggal . ' ' . $bulan[$bulanAngka] . ' ' . $tahun;
}

// Fungsi hitung denda - handle null values
function hitungDenda($tanggal_kembali, $tanggal_dikembalikan = null) {
    // Handle null atau empty
    if (!$tanggal_kembali || $tanggal_kembali === '0000-00-00') {
        return 0;
    }
    
    $tgl_kembali = strtotime($tanggal_kembali);
    
    // Jika strtotime gagal
    if ($tgl_kembali === false) {
        return 0;
    }
    
    // Jika tanggal dikembalikan null, gunakan hari ini
    $tgl_dikembalikan = $tanggal_dikembalikan ? strtotime($tanggal_dikembalikan) : time();
    
    // Jika belum terlambat, return 0
    if ($tgl_dikembalikan <= $tgl_kembali) {
        return 0;
    }
    
    // Hitung selisih hari
    $selisih = ceil(($tgl_dikembalikan - $tgl_kembali) / (60 * 60 * 24));
    
    // Pastikan DENDA_PER_HARI sudah didefinisikan
    $dendaPerHari = defined('DENDA_PER_HARI') ? DENDA_PER_HARI : 1000;
    
    return $selisih * $dendaPerHari;
}

// Fungsi format rupiah - handle null dan invalid values
function formatRupiah($angka) {
    // Handle null, empty, atau non-numeric
    if ($angka === null || $angka === '' || !is_numeric($angka)) {
        return 'Rp 0';
    }
    
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi status badge - untuk styling
function getStatusBadge($status) {
    // Handle null atau empty status
    if (!$status) {
        return 'badge-secondary';
    }
    
    $badges = [
        'dipinjam' => 'badge-info',
        'dikembalikan' => 'badge-success',
        'terlambat' => 'badge-danger'
    ];
    
    return $badges[strtolower($status)] ?? 'badge-secondary';
}

// Fungsi status label - untuk display text
function getStatusLabel($status) {
    // Handle null atau empty status
    if (!$status) {
        return 'Tidak Diketahui';
    }
    
    $labels = [
        'dipinjam' => 'Dipinjam',
        'dikembalikan' => 'Dikembalikan',
        'terlambat' => 'Terlambat'
    ];
    
    return $labels[strtolower($status)] ?? ucfirst($status);
}
?>
