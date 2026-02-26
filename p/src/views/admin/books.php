<?php
$title = "Kelola Buku";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <div class="page-header">
        <h2>Kelola Buku</h2>
        <a href="?page=book-create" class="btn btn-primary">Tambah Buku</a>
    </div>
    
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
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data buku</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $index => $book): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo escape($book['kode_buku']); ?></td>
                                <td><?php echo escape($book['judul']); ?></td>
                                <td><?php echo escape($book['pengarang']); ?></td>
                                <td><?php echo escape($book['penerbit']); ?></td>
                                <td><?php echo escape($book['tahun_terbit']); ?></td>
                                <td><?php echo $book['stok']; ?></td>
                                <td><?php echo escape($book['nama_kategori'] ?? '-'); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?page=book-edit&id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?page=book-delete&id=<?php echo $book['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirmDelete('Yakin ingin menghapus buku ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
