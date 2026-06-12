<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FishermanController;
use App\Http\Controllers\Admin\CatchController;
use App\Http\Controllers\Admin\FishTypeController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;

// Redirect root to dashboard (which will redirect to login if guest)
Route::redirect('/', '/admin/dashboard');

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Dashboard Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fishermen CRUD & Verify
    Route::get('/fishermen', [FishermanController::class, 'index'])->name('fishermen.index');
    Route::post('/fishermen', [FishermanController::class, 'store'])->name('fishermen.store');
    Route::put('/fishermen/{id}', [FishermanController::class, 'update'])->name('fishermen.update');
    Route::delete('/fishermen/{id}', [FishermanController::class, 'destroy'])->name('fishermen.destroy');
    Route::post('/fishermen/{id}/verify', [FishermanController::class, 'verify'])->name('fishermen.verify');

    // Catches Verification & Logs
    Route::get('/catches', [CatchController::class, 'index'])->name('catches.index');
    Route::get('/catches/{id}', [CatchController::class, 'show'])->name('catches.show');
    Route::post('/catches/{id}/verify', [CatchController::class, 'verify'])->name('catches.verify');

    // Master Fish Types CRUD
    Route::get('/fish-types', [FishTypeController::class, 'index'])->name('fish-types.index');
    Route::post('/fish-types', [FishTypeController::class, 'store'])->name('fish-types.store');
    Route::put('/fish-types/{id}', [FishTypeController::class, 'update'])->name('fish-types.update');
    Route::delete('/fish-types/{id}', [FishTypeController::class, 'destroy'])->name('fish-types.destroy');

    // Audit logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// Helper routes for cPanel hosting deployment (can be removed after deployment)
Route::get('/hosting/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migration successful: <br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Migration failed: ' . $e->getMessage();
    }
});

Route::get('/hosting/storage-link', function () {
    try {
        if (file_exists(public_path('storage'))) {
            return 'Storage link already exists.';
        }
        
        $target = storage_path('app/public');
        $shortcut = public_path('storage');
        
        // Attempt to create symlink using PHP's symlink function
        if (symlink($target, $shortcut)) {
            return 'Storage link created successfully from ' . $target . ' to ' . $shortcut;
        }
        return 'Failed to create storage link.';
    } catch (\Exception $e) {
        return 'Failed to create storage link: ' . $e->getMessage();
    }
});


