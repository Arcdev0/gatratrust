<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TblProjectController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Authentication Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');

});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects Resource
    Route::get('/projects', [TblProjectController::class, 'index'])->name('projects.tampilan');
    Route::get('/projects/list', [TblProjectController::class, 'getListProject'])->name('projects.list');
    Route::post('/projects/store', [TblProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/show/{project}', [TblProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/update/{project}', [TblProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/delete/{project}', [TblProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/upload-step-files', [TblProjectController::class, 'uploadFiles']);
    Route::get('/project/{id}/uploaded-files', [TblProjectController::class, 'getUploadedFiles']);
    Route::delete('/project/delete-file/{id}', [TblProjectController::class, 'deleteFile']);
    Route::post('/project/{id}/mark-step-done', [TblProjectController::class, 'markStepDone']);
    Route::post('/project/{id}/unmark-step-done', [TblProjectController::class, 'unmarkStepDone']);

    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user.tampilan');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/update/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
