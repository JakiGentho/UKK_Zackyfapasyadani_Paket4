<?php
require_once '../src/models/User.php';
require_once '../src/models/Book.php';
require_once '../src/models/Loan.php';

class AdminController {
    private $userModel;
    private $bookModel;
    private $loanModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
        $this->bookModel = new Book($db);
        $this->loanModel = new Loan($db);
    }
    
    public function dashboard() {
        requireAdmin();
        
        $totalBooks = $this->bookModel->countBooks();
        $totalUsers = $this->userModel->countUsers();
        $activeLoans = $this->loanModel->countActiveLoans();
        $overdueLoans = $this->loanModel->countOverdueLoans();
        
        include '../src/views/admin/dashboard.php';
    }
}
?>
