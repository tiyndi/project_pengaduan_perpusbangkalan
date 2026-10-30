<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ComplaintController;
use App\Http\Controllers\Public\TrackingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Halaman Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Autentikasi (dengan Rate Limiting & Proteksi Anti Brute-Force)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // max 5 percobaan per menit
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1'); // max 5 pendaftaran per menit

    // Google Sign-In & OAuth 2.0
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::post('/auth/google/signin', [AuthController::class, 'processGoogleSignIn'])
        ->middleware('throttle:10,1')
        ->name('auth.google.signin');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Laporan Pengaduan (butuh login)
Route::middleware('auth')->group(function () {
    Route::get('/laporan/buat', [ComplaintController::class, 'create'])->name('complaint.create');
    Route::post('/laporan/kirim', [ComplaintController::class, 'store'])
        ->middleware('throttle:5,1') // max 5 laporan per menit
        ->name('complaint.store');
    Route::get('/laporan/sukses', [ComplaintController::class, 'success'])->name('complaint.success');

    // Lacak Status
    Route::get('/lacak-laporan', [TrackingController::class, 'index'])->name('tracking.index');
    Route::match(['get', 'post'], '/lacak-laporan/cari', [TrackingController::class, 'show'])->name('tracking.show');
});

/*
|--------------------------------------------------------------------------
| Panel Admin & Petugas
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'petugas'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Laporan
    Route::get('/laporan', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/laporan/{complaint}', [AdminComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/laporan/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update-status');
    Route::post('/laporan/{complaint}/respon', [AdminComplaintController::class, 'addResponse'])->name('complaints.add-response');
    Route::delete('/laporan/{complaint}', [AdminComplaintController::class, 'destroy'])->name('complaints.destroy');

    // Manajemen Kategori
    Route::resource('kategori', CategoryController::class)->parameters([
        'kategori' => 'category',
    ])->names([
        'index'   => 'categories.index',
        'create'  => 'categories.create',
        'store'   => 'categories.store',
        'edit'    => 'categories.edit',
        'update'  => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    // Manajemen Pengguna (hanya admin)
    Route::middleware('admin')->group(function () {
        Route::resource('pengguna', UserController::class)->parameters([
            'pengguna' => 'user',
        ])->names([
            'index'   => 'users.index',
            'create'  => 'users.create',
            'store'   => 'users.store',
            'edit'    => 'users.edit',
            'update'  => 'users.update',
            'destroy' => 'users.destroy',
        ]);
        Route::post('pengguna/{user}/toggle-aktif', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });
});
