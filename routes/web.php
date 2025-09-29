<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// Dashboard utama - redirect berdasarkan role
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (dari Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin,super_admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    
    // Barang routes
    Route::get('/admin/barang', [BarangController::class, 'index'])->name('admin.barang');
    Route::post('/admin/barang', [BarangController::class, 'store'])->name('admin.barang.store');
    Route::post('/admin/barang/update-harga', [BarangController::class, 'updateHarga'])->name('admin.barang.update-harga');
    Route::get('/admin/barang/search', [BarangController::class, 'search'])->name('admin.barang.search');
    Route::post('/admin/barang/{barang}/toggle-status', [BarangController::class, 'toggleStatus'])->name('admin.barang.toggle-status');
    
    // Pengajuan routes
    Route::get('/admin/pengajuan', [PengajuanController::class, 'index'])->name('admin.pengajuan');
    Route::get('/admin/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('admin.pengajuan.show');
    Route::post('/admin/pengajuan/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('admin.pengajuan.approve');
    Route::post('/admin/pengajuan/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('admin.pengajuan.reject');
    
    // Invoice routes
    Route::get('/admin/invoice', [InvoiceController::class, 'index'])->name('admin.invoice');
    Route::post('/admin/invoice/{pengajuan}/generate', [InvoiceController::class, 'generate'])->name('admin.invoice.generate');
    Route::post('/admin/invoice/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('admin.invoice.mark-paid');
    Route::post('/admin/invoice/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('admin.invoice.cancel');
    Route::get('/admin/invoice/{invoice}', [InvoiceController::class, 'show'])->name('admin.invoice.show');
    Route::get('/admin/invoice/{invoice}/print', [InvoiceController::class, 'print'])->name('admin.invoice.print');
    
    Route::get('/admin/laporan', [DashboardController::class, 'laporan'])->name('admin.laporan');
});

// Super Admin routes
Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])->name('superadmin.dashboard');
    Route::get('/super-admin/users', [UserController::class, 'index'])->name('superadmin.users');
    Route::post('/super-admin/users', [UserController::class, 'store'])->name('superadmin.users.store');
    Route::put('/super-admin/users/{user}', [UserController::class, 'update'])->name('superadmin.users.update');
    Route::delete('/super-admin/users/{user}', [UserController::class, 'destroy'])->name('superadmin.users.destroy');
    Route::post('/super-admin/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('superadmin.users.toggle-status');
});

// Staf routes
Route::middleware(['auth', 'verified', 'role:staf'])->group(function () {
    Route::get('/staf/dashboard', [DashboardController::class, 'staf'])->name('staf.dashboard');
    Route::get('/staf/pengajuan', [PengajuanController::class, 'stafPengajuan'])->name('staf.pengajuan');
    Route::post('/staf/pengajuan', [PengajuanController::class, 'store'])->name('staf.pengajuan.store');
    Route::get('/staf/status', [PengajuanController::class, 'stafStatus'])->name('staf.status');
    Route::get('/staf/invoice/{invoice}', [InvoiceController::class, 'showForStaf'])->name('staf.invoice.show');
});

// Fallback route
Route::fallback(function () {
    return redirect('/login');
});
