<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}
function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: '.BASE_URL.'login.php');
        exit;
    }
}
function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: '.BASE_URL.'user/dashboard.php');
        exit;
    }
}
function requireUser(): void {
    requireLogin();
    if (isAdmin()) {
        header('Location: '.BASE_URL.'admin/dashboard.php');
        exit;
    }
}
