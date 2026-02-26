<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// ==================== HOME ==================== //
Route::get('/', [UserController::class, 'home'])->name('index');

// ==================== USER AREA ==================== //
Route::get('/product_details/{id}', [UserController::class, 'productDetails'])->name('product_details');
Route::get('/allproducts', [UserController::class, 'allProducts'])->name('viewallproducts');
Route::get('/home', [UserController::class, 'index'])->name('user.home');
Route::get('/my-orders', [UserController::class, 'index'])->name('user.myorders');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/myorders', [UserController::class, 'myOrders'])->name('myorders');
    Route::get('/addtocart/{id}', [UserController::class, 'addToCart'])->name('add_to_cart');
    Route::get('/cartproducts', [UserController::class, 'cartProducts'])->name('cartproducts');
    Route::get('/removecartproducts/{id}', [UserController::class, 'removeCartProducts'])->name('removecartproducts');
    Route::post('/confirm_order', [UserController::class, 'confirmOrder'])->name('confirm_order');
});

// ==================== PROFILE ==================== //
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== ADMIN AREA ==================== //
Route::middleware(['auth', 'admin'])->group(function () {
    // Category
    Route::get('/add_category', [AdminController::class, 'addCategory'])->name('admin.addcategory');
    Route::post('/add_category', [AdminController::class, 'postAddCategory'])->name('admin.postaddcategory');
    Route::get('/view_category', [AdminController::class, 'viewCategory'])->name('admin.viewcategory');
    Route::get('/delete_category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categorydelete');
    Route::get('/update_category/{id}', [AdminController::class, 'updateCategory'])->name('admin.categoryupdate');
    Route::post('/update_category/{id}', [AdminController::class, 'postUpdateCategory'])->name('admin.postupdatecategory');

    // Product
    Route::get('/add_product', [AdminController::class, 'addProduct'])->name('admin.addproduct');
    Route::post('/add_product', [AdminController::class, 'postAddProduct'])->name('admin.postaddproduct');
    Route::get('/view_product', [AdminController::class, 'viewProduct'])->name('admin.viewproduct');
    Route::get('/deleteproduct/{id}', [AdminController::class, 'deleteProduct'])->name('admin.deleteproduct');
    Route::get('/updateproduct/{id}', [AdminController::class, 'updateProduct'])->name('admin.updateproduct');
    Route::post('/updateproduct/{id}', [AdminController::class, 'postUpdateProduct'])->name('admin.postupdateproduct');
    Route::any('/search', [AdminController::class, 'searchProduct'])->name('admin.searchproduct');

    // Orders
    Route::get('/vieworders', [AdminController::class, 'viewOrders'])->name('admin.vieworders');
    Route::post('/change_status/{id}', [AdminController::class, 'changeStatus'])->name('admin.change_status');
});

Route::get('/memory', function() {
    return [
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage' => memory_get_usage(),
        'memory_peak' => memory_get_peak_usage()
    ];
});


require __DIR__ . '/auth.php';
