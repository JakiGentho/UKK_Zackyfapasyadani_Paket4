<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    setFlash('danger','Buku tidak ditemukan!');
} else {
    $cek = $pdo->prepare(
        "SELECT COUNT(*) FROM borrowings
         WHERE book_id = ? AND status IN ('pending','approved','borrowed')"
    );
    $cek->execute([$id]);
    if ($cek->fetchColumn() > 0) {
        setFlash('danger','Buku tidak dapat dihapus, masih ada peminjaman aktif!');
    } else {
        deleteCover($book['cover']);
        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
        setFlash('success',"Buku <b>{$book['title']}</b> berhasil dihapus!");
    }
}
header('Location: books.php');
exit;
