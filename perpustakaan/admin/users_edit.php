<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { setFlash('danger','Pengguna tidak ditemukan!'); header('Location: users.php'); exit; }

$page_title = 'Edit Pengguna';
$error      = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name    = sanitize($_POST['name']    ?? '');
    $email   = sanitize($_POST['email']   ?? '');
    $phone   = sanitize($_POST['phone']   ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $role    = in_array($_POST['role']??'',['admin','user'],true) ? $_POST['role'] : 'user';
    $status  = in_array($_POST['status']??'',['active','inactive'],true) ? $_POST['status'] : 'active';
    $pass    = $_POST['new_password']         ?? '';
    $confirm = $_POST['confirm_new_password'] ?? '';

    if (!$name || !$email) {
        $error = 'Nama dan email wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif ($pass && strlen($pass) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } elseif ($pass && $pass !== $confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek duplikat email (selain diri sendiri)
        $cek = $pdo->prepare("SELECT id FROM users WHERE email=? AND id!=?");
        $cek->execute([$email,$id]);
        if ($cek->fetch()) {
            $error = 'Email sudah digunakan akun lain!';
        } else {
            if ($pass) {
                $pdo->prepare(
                    "UPDATE users SET name=?,email=?,password=?,phone=?,address=?,role=?,status=? WHERE id=?"
                )->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$phone,$address,$role,$status,$id]);
            } else {
                $pdo->prepare(
                    "UPDATE users SET name=?,email=?,phone=?,address=?,role=?,status=? WHERE id=?"
                )->execute([$name,$email,$phone,$address,$role,$status,$id]);
            }
            setFlash('success',"Akun <b>$name</b> berhasil diperbarui!");
            header('Location: users.php'); exit;
        }
    }
}

include '../includes/admin_sidebar.php';
?>

<?php if ($error): ?>
  <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
<?php endif; ?>

<div class="content-card" style="max-width:640px;">
  <div class="card-header-custom">
    <h6><i class="bi bi-pencil me-2 text-warning"></i>Edit Akun Pengguna</h6>
    <a href="users.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>
  <form method="POST">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-600">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($user['name']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control"
               value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Role</label>
        <select name="role" class="form-select"
          <?= $user['id']==$_SESSION['user_id']?'disabled':'' ?>>
          <option value="user"  <?= $user['role']==='user' ?'selected':'' ?>>👤 User</option>
          <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>🛡️ Admin</option>
        </select>
        <?php if ($user['id']==$_SESSION['user_id']): ?>
          <input type="hidden" name="role" value="<?= $user['role'] ?>">
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Status</label>
        <select name="status" class="form-select"
          <?= $user['id']==$_SESSION['user_id']?'disabled':'' ?>>
          <option value="active"   <?= $user['status']==='active'  ?'selected':'' ?>>✅ Aktif</option>
          <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>>❌ Nonaktif</option>
        </select>
        <?php if ($user['id']==$_SESSION['user_id']): ?>
          <input type="hidden" name="status" value="<?= $user['status'] ?>">
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">No. Telepon</label>
        <input type="text" name="phone" class="form-control"
               value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600">Alamat</label>
        <textarea name="address" class="form-control"
                  rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
      </div>

      <div class="col-12"><hr><p class="text-muted small mb-2">
        <i class="bi bi-lock me-1"></i>Ganti Password (kosongkan jika tidak ingin mengubah)
      </p></div>

      <div class="col-md-6">
        <label class="form-label fw-600 small">Password Baru</label>
        <div class="input-group">
          <input type="password" name="new_password" id="newPass2"
                 class="form-control" placeholder="Min. 6 karakter">
          <button class="btn btn-outline-secondary toggle-password"
                  type="button" data-target="newPass2">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-600 small">Konfirmasi Password Baru</label>
        <input type="password" name="confirm_new_password" class="form-control">
      </div>

      <div class="col-12 pt-2">
        <button type="submit" class="btn btn-warning me-2">
          <i class="bi bi-save me-1"></i>Update Akun
        </button>
        <a href="users.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>

<?php include '../includes/admin_footer.php'; ?>
