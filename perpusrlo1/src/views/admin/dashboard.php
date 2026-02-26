<?php 
$page_title = 'Admin Dashboard - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Buku</h6>
                            <h2 class="mb-0"><?php echo $total_books; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total User</h6>
                            <h2 class="mb-0"><?php echo $total_users; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Peminjaman Aktif</h6>
                            <h2 class="mb-0"><?php echo $active_loans; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-journal-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Denda</h6>
                            <h2 class="mb-0"><?php echo formatRupiah($total_fines); ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Loans -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Peminjaman Terbaru</h5>
        </div>
        <div class="card-body">
            <?php if (empty($recent_loans)): ?>
                <p class="text-muted">Belum ada peminjaman</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_loans as $loan): ?>
                            <tr>
                                <td><?php echo $loan['nama_lengkap']; ?></td>
                                <td><?php echo $loan['judul']; ?></td>
                                <td><?php echo formatDate($loan['tanggal_pinjam']); ?></td>
                                <td><?php echo formatDate($loan['tanggal_jatuh_tempo']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'returned' => 'info',
                                        'overdue' => 'dark'
                                    ];
                                    $status_text = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Dipinjam',
                                        'rejected' => 'Ditolak',
                                        'returned' => 'Dikembalikan',
                                        'overdue' => 'Terlambat'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class[$loan['status']]; ?>">
                                        <?php echo $status_text[$loan['status']]; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
