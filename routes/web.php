<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{book}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');

    Route::middleware('role:admin,pustakawan')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/loans/overdue', [LoanController::class, 'overdue'])->name('loans.overdue');
    });

    Route::middleware('role:anggota')->group(function () {
        Route::get('/my/loans', [LoanController::class, 'memberIndex'])->name('member.loans.index');
        Route::get('/my/loans/{loan}', [LoanController::class, 'memberShow'])->name('member.loans.show');
        Route::get('/my/overdue', [LoanController::class, 'overdue'])->name('member.loans.overdue');
    });

    Route::middleware('role:admin,pustakawan')->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/extend', [LoanController::class, 'extend'])->name('loans.extend');
        Route::post('/loans/{loan}/items/{item}/return', [LoanController::class, 'returnItem'])->name('loans.items.return');
        Route::get('/fines', [FineController::class, 'index'])->name('fines.index');
        Route::post('/fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('authors', AuthorController::class)->except('show');
        Route::resource('publishers', PublisherController::class)->except('show');
        Route::resource('books', BookController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });
});
