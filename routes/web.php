<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/multable', function () {
    return view('multable'); //multable.blade.php
});
Route::get('/even', function () {
    return view('even'); //even.blade.php
});
Route::get('/prime', function () {
    return view('prime'); //prime.blade.php
});

Route::get('products', [ProductsController::class, 'list'])->name('products_list');
Route::get('/products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::get('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
Route::post('/products/{product}/buy', [ProductsController::class, 'buy'])->name('products.buy');
Route::get('/invoice/{order}', [ProductsController::class, 'invoice'])->name('invoice');
Route::get('/insufficient-balance', [ProductsController::class, 'insufficientBalance'])->name('insufficient_balance');



Route::get('/register', [UsersController::class, 'showRegister'])->name('register');
Route::post('/register', [UsersController::class, 'register'])->name('register.post');

Route::get('/login', [UsersController::class, 'showLogin'])->name('login');
Route::post('/login', [UsersController::class, 'login'])->name('login.post');
Route::get('users', [UsersController::class, 'list'])->name('users');
Route::post('/logout', [UsersController::class, 'logout'])->name('logout');
Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
Route::post('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
Route::get('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');
Route::get('/users/update_balance/{user}', [UsersController::class, 'showBalance'])->name('showBalance');
Route::post('/users/update_balance/{user}', [UsersController::class, 'updateBalance'])->name('updateBalance');
Route::post('/users/add_Gift/{user}', [UsersController::class, 'add_Gift'])->name('add_Gift');

Route::get('/users/create-customer', [UsersController::class, 'showCreateCustomer'])->name('users.create_customer');
Route::post('/users/create-customer', [UsersController::class, 'createCustomerByAdmin']);
