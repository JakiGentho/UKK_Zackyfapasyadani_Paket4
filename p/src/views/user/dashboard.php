<?php
$title = "Dashboard User";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2>Dashboard User</h2>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="welcome-card">
        <h3>Selamat Datang, <?php echo escape($_SESSION['nama']); ?>!</h3>
        <p>Selamat datang di Perpustakaan RLO. Gunakan menu di atas untuk meminjam buku atau melihat riwayat peminjaman Anda.</p>
        
        <div class="mt-2">
            <a href="?page=catalog" class="btn btn-primary">Lihat Katalog Buku</a>
            <a href="?page=my-loans" class="btn btn-success">Riwayat Peminjaman</a>
        </div>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
