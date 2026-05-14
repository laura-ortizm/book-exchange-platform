<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/',        [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/contact', fn() => view('contact'))->name('contact');

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout',       [AuthController::class, 'logout'])->name('logout');

    // IMPORTANT: create has to go before {book} or it causes 404:
    Route::get('/books/create',  [BookController::class, 'create'])->name('books.create');
    Route::post('/books',        [BookController::class, 'store'])->name('books.store');

    Route::get('/profile',       [ProfileController::class, 'index'])->name('profile.index');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', fn() => view('admin.categories'))->name('categories');
    Route::get('/disputes',   fn() => view('admin.disputes'))->name('disputes');
});

// Public book detail — must come LAST so /books/create is matched first
// ({book} is a wildcard and would catch /books/create too if placed higher)
Route::get('/books/{book}', [CatalogController::class, 'show'])->name('books.show');