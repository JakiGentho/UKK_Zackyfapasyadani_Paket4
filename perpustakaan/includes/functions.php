<?php
function sanitize(string $data): string {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate(?string $date): string {
    if (!$date || $date === '0000-00-00') return '-';
    $months = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    [$y, $m, $d] = explode('-', substr($date, 0, 10));
    return "$d {$months[(int)$m]} $y";
}

function formatRupiah(float $amount): string {
    return 'Rp '.number_format($amount, 0, ',', '.');
}

function getDaysLate(string $due_date, ?string $return_date = null): int {
    $end   = $return_date ? new DateTime($return_date) : new DateTime('today');
    $start = new DateTime($due_date);
    return $end > $start ? (int)$start->diff($end)->days : 0;
}

function calculateFine(int $days_late): float {
    return (float)($days_late * FINE_PER_DAY);
}

function uploadCover(array $file): string|false {
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true) || $file['size'] > 2_097_152) return false;
    $dir = dirname(__DIR__).'/uploads/covers/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = 'cover_'.uniqid().'.'.$ext;
    return move_uploaded_file($file['tmp_name'], $dir.$name) ? $name : false;
}

function deleteCover(?string $filename): void {
    if ($filename) {
        $path = dirname(__DIR__).'/uploads/covers/'.$filename;
        if (file_exists($path)) unlink($path);
    }
}

function getStatusBadge(string $status): string {
    $map = [
        'pending'  => ['status-pending',  'bi-hourglass-split',      'Menunggu'],
        'approved' => ['status-approved', 'bi-check-circle',          'Disetujui'],
        'rejected' => ['status-rejected', 'bi-x-circle',              'Ditolak'],
        'borrowed' => ['status-borrowed', 'bi-book-half',             'Dipinjam'],
        'returned' => ['status-returned', 'bi-check2-all',            'Dikembalikan'],
        'overdue'  => ['status-overdue',  'bi-exclamation-triangle',  'Terlambat'],
    ];
    [$cls, $icon, $label] = $map[$status] ?? ['bg-secondary text-white','bi-question', ucfirst($status)];
    return "<span class='badge $cls'><i class='bi $icon me-1'></i>$label</span>";
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function showFlash(): void {
    if (!isset($_SESSION['flash'])) return;
    ['type' => $type, 'message' => $msg] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $cls = match($type) {
        'success' => 'alert-success',
        'danger'  => 'alert-danger',
        'warning' => 'alert-warning',
        default   => 'alert-info',
    };
    echo "<div class='alert $cls alert-dismissible auto-dismiss fade show' role='alert'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

function updateOverdue(PDO $pdo): void {
    $pdo->exec("UPDATE borrowings SET status='overdue'
                WHERE status='borrowed' AND due_date < CURDATE()");
}
