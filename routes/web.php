<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExchangeController;

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
    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');

    // IMPORTANT: create has to go before {book} or it causes 404
    Route::get('/books/create',    [BookController::class, 'create'])->name('books.create');
    Route::post('/books',          [BookController::class, 'store'])->name('books.store');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::get('/profile',      [ProfileController::class, 'index'])->name('profile.index');

    // Exchange routes
    Route::post('/exchanges/{book}',             [ExchangeController::class, 'store'])->name('exchanges.store');
    Route::get('/exchanges/{exchange}',          [ExchangeController::class, 'show'])->name('exchanges.show');
    Route::get('/exchanges/{exchange}/choose-book', [ExchangeController::class, 'chooseBook'])->name('exchanges.choose-book');
    Route::post('/exchanges/{exchange}/accept',  [ExchangeController::class, 'accept'])->name('exchanges.accept');
    Route::post('/exchanges/{exchange}/reject',  [ExchangeController::class, 'reject'])->name('exchanges.reject');
    Route::post('/exchanges/{exchange}/confirm', [ExchangeController::class, 'confirm'])->name('exchanges.confirm');
    Route::post('/exchanges/{exchange}/dispute', [ExchangeController::class, 'dispute'])->name('exchanges.dispute');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories',                   [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories',                  [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}',        [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}',     [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/disputes', [AdminController::class, 'disputes'])->name('disputes');
    Route::post('/disputes/{dispute}/accept', [AdminController::class, 'acceptDispute'])->name('disputes.accept');
    Route::post('/disputes/{dispute}/reject', [AdminController::class, 'rejectDispute'])->name('disputes.reject');
});

// Public book detail — must come LAST so /books/create is matched first
// ({book} is a wildcard and would catch /books/create too if placed higher)
Route::get('/books/{book}', [CatalogController::class, 'show'])->name('books.show');
