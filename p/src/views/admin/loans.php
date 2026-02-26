<?php
$title = "Kelola Peminjaman";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2>Data Peminjaman</h2>
    
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
                        <th>User</th>
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
                            <td colspan="8" class="text-center">Belum ada data peminjaman</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($loans as $index => $loan): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo escape($loan['nama_user']); ?></td>
                                <td><?php echo escape($loan['judul_buku']); ?></td>
                                <td><?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                                <td><?php echo formatTanggal($loan['tanggal_kembali']); ?></td>
                                <td><?php echo $loan['tanggal_dikembalikan'] ? formatTanggal($loan['tanggal_dikembalikan']) : '-'; ?></td>
                                <td>
                                    <span class="badge <?php echo getStatusBadge($loan['status']); ?>">
                                        <?php echo getStatusLabel($loan['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $loan['denda'] > 0 ? formatRupiah($loan['denda']) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
