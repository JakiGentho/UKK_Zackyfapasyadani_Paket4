<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireUser();

$page_title = 'Profil Saya';
$uid        = $_SESSION['user_id'];
$error      = '';
$tab        = $_GET['tab'] ?? 'info';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

// Statistik peminjaman
$stats = [
    'total'    => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=?"),
    'active'   => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status IN ('borrowed','overdue')"),
    'returned' => $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id=? AND status='returned'"),
    'fine'     => $pdo->prepare("SELECT COALESCE(SUM(fine),0) FROM borrowings WHERE user_id=? AND status='returned'"),
];
foreach ($stats as $k => $s) {
    $s->execute([$uid]);
    $stats[$k] = $s->fetchColumn();
}

// ── Update Info ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_info'])) {
    $name    = sanitize($_POST['name']    ?? '');
    $phone   = sanitize($_POST['phone']   ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if (!$name) {
        $error = 'Nama tidak boleh kosong!';
        $tab   = 'info';
    } else {
        $pdo->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?")
            ->execute([$name, $phone, $address, $uid]);
        $_SESSION['name'] = $name;
        setFlash('success', 'Profil berhasil diperbarui!');
        header('Location: profile.php?tab=info');
        exit;
    }
}

// ── Ganti Password ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_password'])) {
    $old     = $_POST['old_password']         ?? '';
    $new     = $_POST['new_password']         ?? '';
    $confirm = $_POST['confirm_new_password'] ?? '';
    $tab     = 'password';

    if (!$old || !$new || !$confirm) {
        $error = 'Semua field password wajib diisi!';
    } elseif (!password_verify($old, $user['password'])) {
        $error = 'Password lama tidak sesuai!';
    } elseif (strlen($new) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } elseif ($new !== $confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $uid]);
        setFlash('success', 'Password berhasil diubah!');
        header('Location: profile.php?tab=password');
        exit;
    }
}

include '../includes/user_navbar.php';
?>

<?php showFlash(); ?>
<?php if ($error): ?>
  <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
<?php endif; ?>

<div class="row g-4">

  <!-- Sidebar Profil -->
  <div class="col-lg-4">
    <div class="user-card text-center">
      <!-- Avatar -->
      <div class="profile-avatar mx-auto">
        <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
      </div>
      <h5 class="fw-bold mb-0"><?= htmlspecialchars($user['name']) ?></h5>
      <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
      <span class="badge bg-primary mb-3">👤 Anggota</span>
      <p class="text-muted small mb-0">
        <i class="bi bi-calendar me-1"></i>
        Bergabung: <?= formatDate($user['created_at']) ?>
      </p>
    </div>

    <!-- Stat Box -->
    <div class="user-card">
      <h6 class="user-card-title fw-bold mb-3">
        <i class="bi bi-bar-chart me-2 text-primary"></i>Statistik Saya
      </h6>
      <div class="row g-2 text-center">
        <div class="col-6">
          <div class="p-3 rounded-3 bg-light">
            <div class="fw-bold fs-4 text-primary"><?= $stats['total'] ?></div>
            <small class="text-muted">Total Pinjaman</small>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded-3 bg-light">
            <div class="fw-bold fs-4 text-success"><?= $stats['returned'] ?></div>
            <small class="text-muted">Dikembalikan</small>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded-3 bg-light">
            <div class="fw-bold fs-4 text-warning"><?= $stats['active'] ?></div>
            <small class="text-muted">Aktif</small>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded-3 bg-light">
            <div class="fw-bold text-danger" style="font-size:.95rem;">
              <?= formatRupiah($stats['fine']) ?>
            </div>
            <small class="text-muted">Total Denda</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Nav Tab -->
    <div class="user-card p-2">
      <a href="?tab=info"
         class="btn w-100 text-start mb-1
                <?= $tab==='info' ? 'btn-primary' : 'btn-light' ?>">
        <i class="bi bi-person me-2"></i>Edit Profil
      </a>
      <a href="?tab=password"
         class="btn w-100 text-start
                <?= $tab==='password' ? 'btn-primary' : 'btn-light' ?>">
        <i class="bi bi-lock me-2"></i>Ganti Password
      </a>
    </div>
  </div>

  <!-- Form Area -->
  <div class="col-lg-8">

    <?php if ($tab === 'info'): ?>
    <!-- Edit Info -->
    <div class="user-card">
      <div class="user-card-header">
        <h6 class="user-card-title">
          <i class="bi bi-person me-2 text-primary"></i>Edit Informasi Profil
        </h6>
      </div>
      <form method="POST">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-600">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Email</label>
            <input type="email" class="form-control"
                   value="<?= htmlspecialchars($user['email']) ?>"
                   disabled readonly>
            <small class="text-muted">Email tidak dapat diubah. Hubungi admin jika perlu.</small>
          </div>
          <div class="col-12">
            <label class="form-label fw-600">No. Telepon</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone text-muted"></i></span>
              <input type="text" name="phone" class="form-control"
                     value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                     placeholder="08xxxxxxxxxx">
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Alamat</label>
            <textarea name="address" class="form-control" rows="3"
                      placeholder="Alamat lengkap Anda..."
            ><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
          </div>
          <div class="col-12 pt-2">
            <button type="submit" name="update_info" class="btn btn-gradient">
              <i class="bi bi-save me-2"></i>Simpan Perubahan
            </button>
          </div>
        </div>
      </form>
    </div>

    <?php else: ?>
    <!-- Ganti Password -->
    <div class="user-card">
      <div class="user-card-header">
        <h6 class="user-card-title">
          <i class="bi bi-lock me-2 text-primary"></i>Ganti Password
        </h6>
      </div>
      <form method="POST">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-600">Password Lama <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
              <input type="password" name="old_password" id="oldPass"
                     class="form-control" placeholder="Masukkan password lama" required>
              <button class="btn btn-outline-secondary toggle-password"
                      type="button" data-target="oldPass">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Password Baru <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
              <input type="password" name="new_password" id="newPassProfile"
                     class="form-control" placeholder="Min. 6 karakter" required>
              <button class="btn btn-outline-secondary toggle-password"
                      type="button" data-target="newPassProfile">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div id="strengthBarProfile" class="password-strength mt-1"></div>
            <small id="strengthLabelProfile" class="text-muted"></small>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">
              Konfirmasi Password Baru <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
              <input type="password" name="confirm_new_password" id="confirmPass"
                     class="form-control" placeholder="Ulangi password baru" required>
              <button class="btn btn-outline-secondary toggle-password"
                      type="button" data-target="confirmPass">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div id="matchMsg" class="small mt-1"></div>
          </div>
          <div class="col-12">
            <div class="alert alert-info small py-2 d-flex align-items-center gap-2">
              <i class="bi bi-shield-check"></i>
              <div>
                Password yang kuat: minimal 8 karakter, huruf besar, angka,
                dan karakter spesial.
              </div>
            </div>
          </div>
          <div class="col-12 pt-2">
            <button type="submit" name="change_password" class="btn btn-gradient">
              <i class="bi bi-lock me-2"></i>Ubah Password
            </button>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>

  </div><!-- /.col -->
</div><!-- /.row -->

<?php
$extra_js = <<<JS
<script>
// Strength indicator untuk form ganti password
const newPassP   = document.getElementById('newPassProfile');
const strengthP  = document.getElementById('strengthBarProfile');
const strengthLP = document.getElementById('strengthLabelProfile');
const confirmP   = document.getElementById('confirmPass');
const matchMsg   = document.getElementById('matchMsg');

if (newPassP && strengthP) {
  newPassP.addEventListener('input', function() {
    const v = newPassP.value;
    let score = 0;
    if (v.length >= 6)           score++;
    if (v.length >= 10)          score++;
    if (/[A-Z]/.test(v))         score++;
    if (/[0-9]/.test(v))         score++;
    if (/[^A-Za-z0-9]/.test(v))  score++;

    strengthP.className = 'password-strength';
    if (!v.length) {
      strengthP.style.width = '0';
      strengthLP.textContent = '';
      return;
    }
    if (score <= 2) {
      strengthP.classList.add('strength-weak');
      strengthLP.textContent = 'Lemah';
    } else if (score <= 3) {
      strengthP.classList.add('strength-medium');
      strengthLP.textContent = 'Sedang';
    } else {
      strengthP.classList.add('strength-strong');
      strengthLP.textContent = 'Kuat';
    }
    checkMatch();
  });
}

if (confirmP) {
  confirmP.addEventListener('input', checkMatch);
}

function checkMatch() {
  if (!newPassP || !confirmP || !matchMsg) return;
  if (!confirmP.value) { matchMsg.textContent = ''; return; }
  if (newPassP.value === confirmP.value) {
    matchMsg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Password cocok</span>';
  } else {
    matchMsg.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Password tidak cocok</span>';
  }
}
</script>
JS;
include '../includes/user_footer.php';
?>
