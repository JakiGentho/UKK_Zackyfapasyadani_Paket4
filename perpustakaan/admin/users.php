<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = 'Kelola Pengguna';
$error      = '';

// ── Buat Akun ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_user'])) {
    $name    = sanitize($_POST['name']    ?? '');
    $email   = sanitize($_POST['email']   ?? '');
    $phone   = sanitize($_POST['phone']   ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $role    = in_array($_POST['role']??'',['admin','user']) ? $_POST['role'] : 'user';
    $pass    = $_POST['password']         ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$pass) {
        $error = 'Nama, email, dan password wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($pass !== $confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $cek = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar!';
        } else {
            $pdo->prepare("
                INSERT INTO users (name,email,password,phone,address,role,status)
                VALUES (?,?,?,?,?,'.$role.','active')
            ");
            // Cara yang benar:
            $stmt = $pdo->prepare(
                "INSERT INTO users (name,email,password,phone,address,role,status)
                 VALUES (?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $name, $email, password_hash($pass, PASSWORD_DEFAULT),
                $phone, $address, $role, 'active'
            ]);
            setFlash('success',"Akun <b>$name</b> berhasil dibuat!");
            header('Location: users.php'); exit;
        }
    }
}

// ── Toggle Status ─────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $uid = (int)$_GET['toggle'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $pdo->prepare(
            "UPDATE users SET status=IF(status='active','inactive','active') WHERE id=?"
        )->execute([$uid]);
        setFlash('success','Status pengguna berhasil diubah!');
    }
    header('Location: users.php'); exit;
}

// ── Filter ────────────────────────────────────────────────
$search      = sanitize($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';

$sql    = "SELECT u.*,
             (SELECT COUNT(*) FROM borrowings b WHERE b.user_id=u.id AND b.status IN ('borrowed','overdue')) AS active_loans,
             (SELECT COUNT(*) FROM borrowings b WHERE b.user_id=u.id) AS total_loans
           FROM users u WHERE 1=1";
$params = [];
if ($search) {
    $sql    .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params  = array_merge($params,["%$search%","%$search%","%$search%"]);
}
if (in_array($role_filter,['admin','user'],true)) {
    $sql    .= " AND u.role=?";
    $params[] = $role_filter;
}
$sql .= " ORDER BY u.role ASC, u.name ASC";

$stmt  = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$count_all    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$count_user   = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$count_admin  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$count_active = $pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();

include '../includes/admin_sidebar.php';
?>

<?php showFlash(); ?>

<!-- Stat -->
<div class="row g-3 mb-4">
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-primary">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $count_all ?></div><div class="stat-label">Total Akun</div></div>
        <i class="bi bi-people stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-info">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $count_user ?></div><div class="stat-label">Anggota</div></div>
        <i class="bi bi-person stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-danger">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $count_admin ?></div><div class="stat-label">Admin</div></div>
        <i class="bi bi-shield-check stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card stat-card text-white bg-gradient-success">
      <div class="card-body py-3 d-flex justify-content-between align-items-center">
        <div><div class="stat-number"><?= $count_active ?></div><div class="stat-label">Aktif</div></div>
        <i class="bi bi-person-check stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Form Buat Akun -->
  <div class="col-lg-4">
    <div class="content-card">
      <div class="card-header-custom">
        <h6><i class="bi bi-person-plus me-2 text-primary"></i>Buat Akun Baru</h6>
      </div>
      <?php if ($error): ?>
        <div class="alert alert-danger auto-dismiss py-2 small"><?= $error ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-600 small">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control form-control-sm"
                 value="<?= sanitize($_POST['name']??'') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control form-control-sm"
                 value="<?= sanitize($_POST['email']??'') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-600 small">Role <span class="text-danger">*</span></label>
          <select name="role" class="form-select form-select-sm">
            <option value="user"  <?= ($_POST['role']??'user')==='user' ?'selected':'' ?>>👤 User (Anggota)</option>
            <option value="admin" <?= ($_POST['role']??'')==='admin'    ?'selected':'' ?>>🛡️ Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-600 small">No. Telepon</label>
          <input type="text" name="phone" class="form-control form-control-sm"
                 value="<?= sanitize($_POST['phone']??'') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-600 small">Alamat</label>
          <textarea name="address" class="form-control form-control-sm"
                    rows="2"><?= sanitize($_POST['address']??'') ?></textarea>
        </div>
        <hr class="my-3">
        <div class="mb-3">
          <label class="form-label fw-600 small">Password <span class="text-danger">*</span></label>
          <div class="input-group input-group-sm">
            <input type="password" name="password" id="newPass"
                   class="form-control" placeholder="Min. 6 karakter" required>
            <button class="btn btn-outline-secondary toggle-password"
                    type="button" data-target="newPass">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div id="strengthBar" class="password-strength mt-1"></div>
          <small id="strengthLabel" class="text-muted"></small>
        </div>
        <div class="mb-4">
          <label class="form-label fw-600 small">Konfirmasi Password <span class="text-danger">*</span></label>
          <input type="password" name="confirm_password"
                 class="form-control form-control-sm" required>
        </div>
        <button type="submit" name="create_user" class="btn btn-primary w-100">
          <i class="bi bi-person-check me-2"></i>Buat Akun
        </button>
      </form>
    </div>
  </div>

  <!-- Tabel Pengguna -->
  <div class="col-lg-8">
    <div class="content-card">
      <div class="card-header-custom">
        <h6><i class="bi bi-people me-2 text-primary"></i>Daftar Pengguna</h6>
        <span class="badge bg-secondary"><?= count($users) ?></span>
      </div>
      <!-- Filter -->
      <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
          <input type="text" name="search" class="form-control form-control-sm"
                 placeholder="Cari nama, email, telepon..."
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
          <select name="role" class="form-select form-select-sm">
            <option value="">Semua Role</option>
            <option value="user"  <?= $role_filter==='user' ?'selected':'' ?>>User</option>
            <option value="admin" <?= $role_filter==='admin'?'selected':'' ?>>Admin</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-1">
          <button class="btn btn-outline-primary btn-sm flex-fill">
            <i class="bi bi-search"></i>
          </button>
          <a href="users.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x"></i>
          </a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover align-middle small">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Nama & Email</th><th>Role</th>
              <th>Telepon</th><th>Pinjaman</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($users): foreach ($users as $i => $u): ?>
            <tr class="<?= $u['status']==='inactive'?'opacity-50':'' ?>">
              <td class="text-muted"><?= $i+1 ?></td>
              <td>
                <div class="fw-semibold"><?= htmlspecialchars($u['name']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
              </td>
              <td>
                <?php if ($u['role']==='admin'): ?>
                  <span class="badge" style="background:#f5576c;">🛡️ Admin</span>
                <?php else: ?>
                  <span class="badge bg-primary">👤 User</span>
                <?php endif; ?>
              </td>
              <td><?= $u['phone'] ? htmlspecialchars($u['phone']) : '<span class="text-muted">-</span>' ?></td>
              <td>
                <?php if ($u['role']==='user'): ?>
                  <span class="badge bg-warning text-dark me-1"><?= $u['active_loans'] ?> aktif</span>
                  <span class="badge bg-light text-dark border"><?= $u['total_loans'] ?> total</span>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?= $u['status']==='active'?'bg-success':'bg-secondary' ?>">
                  <?= $u['status']==='active'?'Aktif':'Nonaktif' ?>
                </span>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="users_edit.php?id=<?= $u['id'] ?>"
                     class="btn btn-sm btn-outline-warning" title="Edit"
                     data-bs-toggle="tooltip">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="?toggle=<?= $u['id'] ?>"
                       class="btn btn-sm <?= $u['status']==='active'?'btn-outline-secondary':'btn-outline-success' ?>"
                       data-confirm="<?= $u['status']==='active'?'Nonaktifkan':'Aktifkan' ?> akun <?= htmlspecialchars(addslashes($u['name'])) ?>?"
                       data-bs-toggle="tooltip"
                       title="<?= $u['status']==='active'?'Nonaktifkan':'Aktifkan' ?>">
                      <i class="bi <?= $u['status']==='active'?'bi-person-x':'bi-person-check' ?>"></i>
                    </a>
                    <a href="users_delete.php?id=<?= $u['id'] ?>"
                       class="btn btn-sm btn-outline-danger"
                       data-confirm="Hapus akun <?= htmlspecialchars(addslashes($u['name'])) ?>?"
                       data-bs-toggle="tooltip" title="Hapus">
                      <i class="bi bi-trash"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7">
              <div class="empty-state">
                <i class="bi bi-people"></i><p>Tidak ada pengguna ditemukan</p>
              </div>
            </td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$extra_js = <<<JS
<script>
// Gunakan auth.js strength indicator untuk form buat akun
const newPass     = document.getElementById('newPass');
const strengthBar = document.getElementById('strengthBar');
const strengthLbl = document.getElementById('strengthLabel');
if (newPass && strengthBar) {
  newPass.addEventListener('input', function() {
    const v = newPass.value;
    let score = 0;
    if (v.length >= 6)          score++;
    if (v.length >= 10)         score++;
    if (/[A-Z]/.test(v))        score++;
    if (/[0-9]/.test(v))        score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    strengthBar.className = 'password-strength';
    if (!v.length) { strengthBar.style.width='0'; strengthLbl.textContent=''; return; }
    if (score <= 2) { strengthBar.classList.add('strength-weak');   strengthLbl.textContent='Lemah'; }
    else if (score <= 3) { strengthBar.classList.add('strength-medium'); strengthLbl.textContent='Sedang'; }
    else { strengthBar.classList.add('strength-strong'); strengthLbl.textContent='Kuat'; }
  });
}
</script>
JS;
include '../includes/admin_footer.php';
?>
