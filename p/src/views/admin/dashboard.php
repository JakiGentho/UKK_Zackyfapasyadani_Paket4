<?php
$title = "Admin Dashboard";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2>Dashboard Admin</h2>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $totalBooks; ?></h3>
            <p>Total Buku</p>
        </div>
        
        <div class="stat-card">
            <h3><?php echo $totalUsers; ?></h3>
            <p>Total User</p>
        </div>
        
        <div class="stat-card">
            <h3><?php echo $activeLoans; ?></h3>
            <p>Sedang Dipinjam</p>
        </div>
        
        <div class="stat-card">
            <h3><?php echo $overdueLoans; ?></h3>
            <p>Terlambat</p>
        </div>
    </div>
    
    <div class="welcome-card">
        <h3>Selamat Datang, <?php echo escape($_SESSION['nama']); ?>!</h3>
        <p>Gunakan menu navigasi di atas untuk mengelola sistem perpustakaan.</p>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
