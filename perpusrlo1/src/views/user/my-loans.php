<?php 
$page_title = 'Peminjaman Saya - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-journal-check"></i> Peminjaman Saya</h2>
    
    <?php if (empty($loans)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Anda belum memiliki riwayat peminjaman
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $loan['judul']; ?></strong><br>
                                    <small class="text-muted"><?php echo $loan['pengarang']; ?></small>
                                </td>
                                <td><?php echo formatDate($loan['tanggal_pinjam']); ?></td>
                                <td>
                                    <?php 
                                    echo formatDate($loan['tanggal_jatuh_tempo']);
                                    
                                    // Warning jika mendekati jatuh tempo
                                    if ($loan['status'] == 'approved') {
                                        $days_left = calculateDaysBetween(date('Y-m-d'), $loan['tanggal_jatuh_tempo']);
                                        if ($days_left <= 2 && $days_left >= 0) {
                                            echo '<br><span class="badge bg-warning">Segera Jatuh Tempo</span>';
                                        } elseif ($days_left < 0) {
                                            echo '<br><span class="badge bg-danger">Terlambat</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo formatDate($loan['tanggal_kembali']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'returned' => 'info'
                                    ];
                                    $status_text = [
                                        'pending' => 'Menunggu Persetujuan',
                                        'approved' => 'Sedang Dipinjam',
                                        'rejected' => 'Ditolak',
                                        'returned' => 'Dikembalikan'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class[$loan['status']]; ?>">
                                        <?php echo $status_text[$loan['status']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($loan['denda'] > 0): ?>
                                        <span class="text-danger fw-bold"><?php echo formatRupiah($loan['denda']); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $loan['keterangan'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/src/views/layouts/footer.php'; ?>
