<?php
$title = "Kelola User";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <div class="page-header">
        <h2>Kelola User</h2>
        <a href="?page=user-create" class="btn btn-primary">Tambah User</a>
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
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data user</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo escape($user['username']); ?></td>
                                <td><?php echo escape($user['nama']); ?></td>
                                <td><?php echo escape($user['email']); ?></td>
                                <td><?php echo ucfirst($user['role']); ?></td>
                                <td><?php echo formatTanggal($user['created_at']); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?page=user-edit&id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="?page=user-delete&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirmDelete('Yakin ingin menghapus user ini?')">Hapus</a>
                                        <?php endif; ?>
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
