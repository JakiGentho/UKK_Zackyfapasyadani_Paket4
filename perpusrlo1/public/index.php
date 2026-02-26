<?php
require_once '../config/config.php';

// Get page parameter
$page = $_GET['page'] ?? 'login';

// Route handling
switch ($page) {
    // Auth Routes
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    
    // Admin Routes
    case 'admin/dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;
        
    case 'admin/books':
        $controller = new BookController();
        $controller->index();
        break;
        
    case 'admin/book/create':
        $controller = new BookController();
        $controller->create();
        break;
        
    case 'admin/book/store':
        $controller = new BookController();
        $controller->store();
        break;
        
    case 'admin/book/edit':
        $controller = new BookController();
        $controller->edit($_GET['id']);
        break;
        
    case 'admin/book/update':
        $controller = new BookController();
        $controller->update($_GET['id']);
        break;
        
    case 'admin/book/delete':
        $controller = new BookController();
        $controller->delete($_GET['id']);
        break;
        
    case 'admin/loans':
        $controller = new LoanController();
        $controller->index();
        break;
        
    case 'admin/loan/approve':
        $controller = new LoanController();
        $controller->approve($_GET['id']);
        break;
        
    case 'admin/loan/reject':
        $controller = new LoanController();
        $controller->reject($_GET['id']);
        break;
        
    case 'admin/loan/return':
        $controller = new LoanController();
        $controller->returnBook($_GET['id']);
        break;
        
    case 'admin/users':
        $controller = new AdminController();
        $controller->users();
        break;
        
    case 'admin/user/create':
        $controller = new AdminController();
        $controller->createUser();
        break;
        
    case 'admin/user/store':
        $controller = new AdminController();
        $controller->storeUser();
        break;
        
    case 'admin/user/edit':
        $controller = new AdminController();
        $controller->editUser($_GET['id']);
        break;
        
    case 'admin/user/update':
        $controller = new AdminController();
        $controller->updateUser($_GET['id']);
        break;
        
    case 'admin/user/delete':
        $controller = new AdminController();
        $controller->deleteUser($_GET['id']);
        break;
        
    case 'admin/categories':
        $controller = new AdminController();
        $controller->categories();
        break;
        
    case 'admin/category/store':
        $controller = new AdminController();
        $controller->storeCategory();
        break;
        
    case 'admin/category/delete':
        $controller = new AdminController();
        $controller->deleteCategory($_GET['id']);
        break;
    
    // User Routes
    case 'user/dashboard':
        $controller = new UserController();
        $controller->dashboard();
        break;
        
    case 'user/catalog':
        $controller = new UserController();
        $controller->catalog();
        break;
        
    case 'user/my-loans':
        $controller = new LoanController();
        $controller->myLoans();
        break;
        
    case 'user/loan/request':
        $controller = new LoanController();
        $controller->request($_GET['book_id']);
        break;
        
    case 'user/profile':
        $controller = new UserController();
        $controller->profile();
        break;
        
    case 'user/profile/update':
        $controller = new UserController();
        $controller->updateProfile();
        break;
        
    case 'user/password/change':
        $controller = new UserController();
        $controller->changePassword();
        break;
    
    default:
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
?>
