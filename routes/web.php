<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TblProjectController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects Resource
    route::get('/projects', [TblProjectController::class, 'index'])->name('projects.tampilan');
    route::get('/projects/create', [TblProjectController::class, 'create'])->name('projects.create');
    route::post('/projects', [TblProjectController::class, 'store'])->name('projects.store');
    route::get('/projects/{project}', [TblProjectController::class, 'show'])->name('projects.show');
    route::get('/projects/{project}/edit', [TblProjectController::class, 'edit'])->name('projects.edit');
    route::put('/projects/{project}', [TblProjectController::class, 'update'])->name('projects.update');
    route::delete('/projects/{project}', [TblProjectController::class, 'destroy'])->name('projects.destroy');

    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user.tampilan');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

});
