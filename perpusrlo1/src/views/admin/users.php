<?php 
$page_title = 'Kelola User - Perpustakaan RLO';
require_once ROOT_PATH . '/src/views/layouts/header.php';
require_once ROOT_PATH . '/src/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Kelola User</h2>
        <a href="<?php echo BASE_URL; ?>/index.php?page=admin/user/create" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah User
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($users)): ?>
                <p class="text-muted text-center">Belum ada user terdaftar</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong><?php echo $user['username']; ?></strong></td>
                                <td><?php echo $user['nama_lengkap']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['no_telp'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin/user/edit&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin/user/delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
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
