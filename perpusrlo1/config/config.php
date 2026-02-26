<?php
// Base URL configuration
define('BASE_URL', 'http://localhost/perpusrlo/public');
define('SITE_NAME', 'Perpustakaan RLO');

// Path configuration
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/');
define('COVER_PATH', ROOT_PATH . '/public/assets/images/covers/');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/src/models/',
        ROOT_PATH . '/src/controllers/',
        ROOT_PATH . '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include helper files
require_once ROOT_PATH . '/src/helpers/functions.php';
require_once ROOT_PATH . '/src/helpers/session.php';
require_once ROOT_PATH . '/src/helpers/validation.php';
?>
