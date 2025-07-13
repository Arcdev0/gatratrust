<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KerjaanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TblProjectController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::controller(LoginController::class)->group(function () {
    Route::post('/auth', 'login')->name('login.auth');
    Route::get('/login', 'showLoginForm')->name('login');
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
    Route::get('/project-detail/comments', [TblProjectController::class, 'getListKomentar']);
    Route::post('/project-detail/comments/store', [TblProjectController::class, 'storeKomentar'])->name('project.comments.store');
    Route::delete('/project-detail/comments/{id}', [TblProjectController::class, 'deleteKomentar']);
    Route::post('/upload-administrasi-file', [TblProjectController::class, 'uploadFileAdministrasi']);
    Route::get('/get-administrasi-files/{id}', [TblProjectController::class, 'getDataAdministrasi']);
    Route::delete('/administrasi-files/{id}', [TblProjectController::class, 'deleteAdministrasiFile']);





    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user.tampilan');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/update/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


    // List Proses
    Route::get('/listproses', [App\Http\Controllers\ListProsesController::class, 'index'])->name('listproses.index');
    route::post('/listproses/store', [App\Http\Controllers\ListProsesController::class, 'store'])->name('listproses.store');
    Route::post('/listproses/update/{id}', [App\Http\Controllers\ListProsesController::class, 'update'])->name('listproses.update');
    Route::delete('/listproses/delete/{id}', [App\Http\Controllers\ListProsesController::class, 'destroy'])->name('listproses.destroy');

    // Input Project
    Route::get('/kerjaan', [KerjaanController::class, 'index'])->name('kerjaan.show');
    Route::get('/kerjaan/data', [KerjaanController::class, 'getData'])->name('kerjaan.data');
    Route::post('/kerjaan/store', [KerjaanController::class, 'store'])->name('kerjaan.store');
    Route::delete('/kerjaan/{id}', [KerjaanController::class, 'destroy'])->name('kerjaan.destroy');
    Route::get('/kerjaan/edit/{id}', [KerjaanController::class, 'show'])->name('kerjaan.edit');
    Route::put('/kerjaan/update/{id}', [KerjaanController::class, 'update'])->name('kerjaan.update');
});
