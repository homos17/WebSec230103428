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
Route::post('products/save/{product?}', [ProductsController::class,
'save'])->name('products_save');
Route::get('products/delete/{product}', [ProductsController::class,
'delete'])->name('products_delete');



Route::get('/register', [UsersController::class, 'showRegister'])->name('register');
Route::post('/register', [UsersController::class, 'register'])->name('register.post');

Route::get('/login', [UsersController::class, 'showLogin'])->name('login');
Route::post('/login', [UsersController::class, 'login'])->name('login.post');

Route::post('/logout', [UsersController::class, 'logout'])->name('logout');

