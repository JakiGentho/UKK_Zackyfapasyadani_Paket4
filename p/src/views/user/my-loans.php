<?php
$title = "Peminjaman Saya";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2>Peminjaman Saya</h2>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Tgl Dikembalikan</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Anda belum memiliki riwayat peminjaman</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($loans as $index => $loan): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><?php echo escape($loan['judul_buku'] ?? $loan['judul'] ?? '-'); ?></strong><br>
                                    <small><?php echo escape($loan['pengarang_buku'] ?? $loan['pengarang'] ?? '-'); ?></small>
                                </td>
                                <td><?php echo formatTanggal($loan['tanggal_pinjam'] ?? null); ?></td>
                                <td><?php echo formatTanggal($loan['tanggal_kembali'] ?? null); ?></td>
                                <td>
                                    <?php echo isset($loan['tanggal_dikembalikan']) && $loan['tanggal_dikembalikan'] 
                                        ? formatTanggal($loan['tanggal_dikembalikan']) 
                                        : '-'; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo getStatusBadge($loan['status'] ?? 'dipinjam'); ?>">
                                        <?php echo getStatusLabel($loan['status'] ?? 'dipinjam'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo ($loan['denda'] ?? 0) > 0 ? formatRupiah($loan['denda']) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="info-box mt-2">
        <h3>Informasi</h3>
        <ul>
            <li>Masa peminjaman buku adalah <strong>7 hari</strong></li>
            <li>Denda keterlambatan <strong>Rp 1.000 per hari</strong></li>
            <li>Kembalikan buku tepat waktu agar tidak terkena denda</li>
        </ul>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
