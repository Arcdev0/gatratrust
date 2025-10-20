<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KerjaanController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TblProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Models\KaryawanData;
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
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

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
    Route::delete('/project-detail/administrasi-files/{id}', [TblProjectController::class, 'deleteAdministrasiFile']);
    Route::get('/projects/generate-no', [TblProjectController::class, 'generateNoProject']);

    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user.tampilan');
    Route::get('/user/getListTable', [UserController::class, 'getListTable'])->name('user.list');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/update/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

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

    // Daily
    Route::prefix('daily')->group(function () {
        Route::get('/', [DailyController::class, 'index'])->name('daily.index');
        Route::get('/list', [DailyController::class, 'getList'])->name('daily.getList');
        Route::post('/store', [DailyController::class, 'store'])->name('daily.store');
        Route::get('/edit/{id}', [DailyController::class, 'edit'])->name('daily.edit');
        Route::put('/update/{id}', [DailyController::class, 'update'])->name('daily.update');
        Route::delete('/delete/{id}', [DailyController::class, 'destroy'])->name('daily.destroy');
    });
    Route::get('/daily/{daily}/comments', [DailyController::class, 'dataDailyComments']);
    Route::post('/daily/{daily}/comments', [DailyController::class, 'storeDailyComments']);
    Route::delete('/daily/comments/{comment}', [DailyController::class, 'destroyDailyComments']);

    Route::prefix('timeline')->group(function () {
        Route::get('/get', [DailyController::class, 'getDataTimeline'])->name('daily.timeline.get');
        Route::post('/add', [DailyController::class, 'tambahListTimeline'])->name('daily.timeline.add');
        Route::put('/update/{id}', [DailyController::class, 'updateListTimeline'])->name('daily.timeline.update');
        Route::delete('/delete/{id}', [DailyController::class, 'deleteListTimeline'])->name('daily.timeline.delete');
    });

    // Accounting
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/accounting/data', [AccountingController::class, 'data'])->name('accounting.data');
    Route::get('/accounting/create', [AccountingController::class, 'create'])->name('accounting.create');
    Route::get('/accounting/{id}/show', [AccountingController::class, 'show'])->name('accounting.show');
    Route::get('/accounting/generate-no', [AccountingController::class, 'generateNoJurnal'])->name('accounting.generateNo');
    Route::post('/accounting/store', [AccountingController::class, 'store'])->name('accounting.store');
    Route::get('/accounting/{accounting}/edit', [AccountingController::class, 'edit'])->name('accounting.edit');
    Route::post('/accounting/{accounting}/update', [AccountingController::class, 'update'])->name('accounting.update');
    Route::delete('/accounting/{accounting}/delete', [AccountingController::class, 'destroy'])->name('accounting.delete');
    Route::delete('/accounting/file/{file}', [AccountingController::class, 'deleteFile'])->name('accounting.file.delete');
    Route::post('/accounting/import', [AccountingController::class, 'import'])->name('accounting.import');


    // Data karyawan
    Route::prefix('karyawan')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::get('/data', [KaryawanController::class, 'getData'])->name('karyawan.data');
        Route::get('/create', [KaryawanController::class, 'create'])->name('karyawan.create');
        Route::post('/store', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::get('/edit/{id}', [KaryawanController::class, 'edit'])->name('karyawan.edit');
        Route::put('/update/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
        Route::delete('/delete/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
        Route::get('/show/{id}', [KaryawanController::class, 'show'])->name('karyawan.show');
        Route::get('/jabatan/{id}/syarat', [KaryawanController::class, 'getSyaratJabatan'])->name('karyawan.getSyaratJabatan');
    });

    // Jabatan
    Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/jabatan/data', [JabatanController::class, 'getData'])->name('jabatan.data');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/jabatan/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/jabatan/{id}', [JabatanController::class, 'update'])->name('jabatan.update');

    Route::prefix('quotations')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/data', [QuotationController::class, 'getDataTable'])->name('quotations.getDataTable');
        Route::get('/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::get('/show/{id}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::post('/store', [QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/edit/{id}', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::post('/update/{id}', [QuotationController::class, 'update'])->name('quotations.update');
        Route::delete('/delete/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
        Route::get('/export-pdf/{id}', [QuotationController::class, 'exportPdf'])->name('quotations.exportPdf');
    });
    Route::post('/quotations/{id}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('/quotations/{id}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::get('/quotations/{id}/copy', [QuotationController::class, 'copy'])->name('quotations.copy');

    // Invoices
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/data', [InvoiceController::class, 'getData'])->name('invoice.data');
    Route::get('/invoice/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('/invoice/{id}/update', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::get('/invoice/{id}/print', [InvoiceController::class, 'printInvoice'])->name('invoice.print');
    Route::delete('/invoice/delete/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/projects/{project}/dp-invoices', [InvoiceController::class, 'getDpInvoices']);



    // Kwitansi
    Route::get('/kwitansi', [KwitansiController::class, 'index'])->name('kwitansi.index');
    Route::get('/kwitansi/create', [KwitansiController::class, 'create'])->name('kwitansi.create');
    Route::post('/kwitansi/store', [KwitansiController::class, 'store'])->name('kwitansi.store');
    Route::get('/kwitansi/data', [KwitansiController::class, 'data'])->name('kwitansi.data');
    Route::get('/kwitansi/{id}/edit', [KwitansiController::class, 'edit'])->name('kwitansi.edit');
    Route::post('/kwitansi/{id}/update', [KwitansiController::class, 'update'])->name('kwitansi.update');

    //Log
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('activity-logs.data');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');


    Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('/vendor/data', [VendorController::class, 'getData'])->name('vendor.getData');
    Route::get('/vendor/{id}', [VendorController::class, 'show'])->name('vendor.show');
    Route::post('/vendor', [VendorController::class, 'store'])->name('vendor.store');
    Route::put('/vendor/{id}', [VendorController::class, 'update'])->name('vendor.update');
    Route::delete('/vendor/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');


    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/quotation/approval/{encryptedData}', [QuotationController::class, 'showApproval'])->name('quotation.approval');
