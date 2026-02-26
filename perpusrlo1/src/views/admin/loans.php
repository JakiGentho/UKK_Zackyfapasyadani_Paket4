<?php 
$page_title = 'Kelola Peminjaman - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-journal-check"></i> Kelola Peminjaman</h2>
    
    <!-- Filter Status -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loans" 
                   class="btn btn-<?php echo !isset($_GET['status']) ? 'primary' : 'outline-primary'; ?>">
                    Semua
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loans&status=pending" 
                   class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'warning' : 'outline-warning'; ?>">
                    Pending
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loans&status=approved" 
                   class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'success' : 'outline-success'; ?>">
                    Dipinjam
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loans&status=returned" 
                   class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'returned') ? 'info' : 'outline-info'; ?>">
                    Dikembalikan
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loans&status=rejected" 
                   class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'danger' : 'outline-danger'; ?>">
                    Ditolak
                </a>
            </div>
        </div>
    </div>
    
    <!-- Loans Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($loans)): ?>
                <p class="text-muted text-center">Tidak ada data peminjaman</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $loan['nama_lengkap']; ?></strong><br>
                                    <small class="text-muted"><?php echo $loan['username']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $loan['judul']; ?></strong><br>
                                    <small class="text-muted"><?php echo $loan['pengarang']; ?></small>
                                </td>
                                <td><?php echo formatDate($loan['tanggal_pinjam']); ?></td>
                                <td><?php echo formatDate($loan['tanggal_jatuh_tempo']); ?></td>
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
                                        'pending' => 'Menunggu',
                                        'approved' => 'Dipinjam',
                                        'rejected' => 'Ditolak',
                                        'returned' => 'Dikembalikan'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class[$loan['status']]; ?>">
                                        <?php echo $status_text[$loan['status']]; ?>
                                    </span>
                                </td>
                                <td><?php echo $loan['denda'] > 0 ? formatRupiah($loan['denda']) : '-'; ?></td>
                                <td>
                                    <?php if ($loan['status'] == 'pending'): ?>
                                        <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loan/approve&id=<?php echo $loan['id']; ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $loan['id']; ?>">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?php echo $loan['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Peminjaman</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=admin/loan/reject&id=<?php echo $loan['id']; ?>">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Alasan Penolakan</label>
                                                                <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($loan['status'] == 'approved'): ?>
                                        <a href="<?php echo BASE_URL; ?>/index.php?page=admin/loan/return&id=<?php echo $loan['id']; ?>" 
                                           class="btn btn-sm btn-info"
                                           onclick="return confirm('Proses pengembalian buku ini?')">
                                            <i class="bi bi-arrow-return-left"></i> Kembalikan
                                        </a>
                                    <?php endif; ?>
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
