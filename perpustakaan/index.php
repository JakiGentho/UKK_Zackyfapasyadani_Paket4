<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: '.BASE_URL.(isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
} else {
    header('Location: '.BASE_URL.'login.php');
}
exit;
