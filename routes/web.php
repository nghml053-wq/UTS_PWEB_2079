<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - UTS Pemrograman Web 2026
|--------------------------------------------------------------------------
| Seluruh route dikelola melalui PageController sesuai arsitektur MVC murni.
| Data username & parameter dikirimkan melalui Request Parameter.
*/

// Redirect root ke halaman login simulasi
Route::redirect('/', '/login');

// Route Halaman Login Simulasi (GET & POST)
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::post('/login', [PageController::class, 'loginProcess'])->name('login.process');

// Route Dashboard Utama (menerima username via query parameter)
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

// Route Halaman Pengelolaan Data Inventaris (me-render array list dinamis)
Route::get('/pengelolaan', [PageController::class, 'pengelolaan'])->name('pengelolaan');

// Route Halaman Profil Pengguna (menerima data username via query parameter)
Route::get('/profile', [PageController::class, 'profile'])->name('profile');

Route::get('/pengelolaan/export', [PageController::class, 'exportInventory'])->name('pengelolaan.export');
Route::get('/jadwal', [PageController::class, 'jadwal'])->name('jadwal');

Route::post('/logout', [PageController::class, 'logout'])->name('logout');
