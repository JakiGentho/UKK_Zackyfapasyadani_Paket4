<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($id === (int)$_SESSION['user_id']) {
    setFlash('danger','Tidak dapat menghapus akun sendiri!');
    header('Location: users.php'); exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('danger','Pengguna tidak ditemukan!');
} else {
    $cek = $pdo->prepare(
        "SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status IN ('borrowed','overdue')"
    );
    $cek->execute([$id]);
    if ($cek->fetchColumn() > 0) {
        setFlash('danger','Tidak dapat menghapus, pengguna masih memiliki peminjaman aktif!');
    } else {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        setFlash('success',"Akun <b>{$user['name']}</b> berhasil dihapus!");
    }
}
header('Location: users.php');
exit;
