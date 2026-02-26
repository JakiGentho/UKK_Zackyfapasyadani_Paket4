<?php
session_start();

require_once '../config/database.php';
require_once '../config/config.php';
require_once '../src/helpers/session.php';
require_once '../src/helpers/functions.php';
require_once '../src/helpers/validation.php';

$db = getConnection();

$page = $_GET['page'] ?? 'login';

require_once '../src/controllers/AuthController.php';
require_once '../src/controllers/AdminController.php';
require_once '../src/controllers/UserController.php';
require_once '../src/controllers/BookController.php';
require_once '../src/controllers/LoanController.php';

switch($page) {
    case 'login':
        $controller = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'logout':
        $controller = new AuthController($db);
        $controller->logout();
        break;
        
    case 'admin-dashboard':
        $controller = new AdminController($db);
        $controller->dashboard();
        break;
        
    case 'books':
        $controller = new BookController($db);
        $controller->index();
        break;
        
    case 'book-create':
        $controller = new BookController($db);
        $controller->create();
        break;
        
    case 'book-edit':
        $controller = new BookController($db);
        $controller->edit();
        break;
        
    case 'book-delete':
        $controller = new BookController($db);
        $controller->delete();
        break;
        
    case 'users':
        $controller = new UserController($db);
        $controller->index();
        break;
        
    case 'user-create':
        $controller = new UserController($db);
        $controller->create();
        break;
        
    case 'user-edit':
        $controller = new UserController($db);
        $controller->edit();
        break;
        
    case 'user-delete':
        $controller = new UserController($db);
        $controller->delete();
        break;
        
    case 'loans':
        $controller = new LoanController($db);
        $controller->index();
        break;
        
    case 'user-dashboard':
        $controller = new UserController($db);
        $controller->dashboard();
        break;
        
    case 'catalog':
        $controller = new LoanController($db);
        $controller->catalog();
        break;
        
    case 'borrow':
        $controller = new LoanController($db);
        $controller->borrow();
        break;
        
    case 'my-loans':
        $controller = new LoanController($db);
        $controller->myLoans();
        break;
        
    case 'return-book':
        $controller = new LoanController($db);
        $controller->returnBook();
        break;
        
    default:
        header('Location: ?page=login');
        break;
}
?>
