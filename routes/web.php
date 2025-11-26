<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\SPAController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes - Redirect root ke dashboard
Route::redirect('/', '/dashboard');

// Auth Routes (Login, Register, Password Reset, etc.)
require __DIR__.'/auth.php';

// Protected Routes (Require Authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard - Accessible by ALL roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management - Accessible by ALL roles
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    
    // ===== USER MANAGEMENT (ALL USERS) =====
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdministratorController::class, 'userIndex'])->name('index');
        Route::get('/create', [AdministratorController::class, 'userCreate'])->name('create');
        Route::post('/', [AdministratorController::class, 'userStore'])->name('store');
        Route::get('/{id}/edit', [AdministratorController::class, 'userEdit'])->name('edit');
        Route::put('/{id}', [AdministratorController::class, 'userUpdate'])->name('update');
        Route::delete('/{id}', [AdministratorController::class, 'userDestroy'])->name('destroy');
        
        // Quick actions
        Route::post('/{id}/change-role', [AdministratorController::class, 'changeRole'])->name('change-role');
        
        // Bulk actions
        Route::post('/bulk-delete', [AdministratorController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-change-role', [AdministratorController::class, 'bulkChangeRole'])->name('bulk-change-role');
    });
    
    // ===== MAHASISWA MANAGEMENT (LEGACY) =====
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [AdministratorController::class, 'mahasiswaIndex'])->name('index');
        Route::get('/create', [AdministratorController::class, 'mahasiswaCreate'])->name('create');
        Route::post('/', [AdministratorController::class, 'mahasiswaStore'])->name('store');
        Route::get('/{id}/edit', [AdministratorController::class, 'mahasiswaEdit'])->name('edit');
        Route::put('/{id}', [AdministratorController::class, 'mahasiswaUpdate'])->name('update');
        Route::delete('/{id}', [AdministratorController::class, 'mahasiswaDestroy'])->name('destroy');
    });
    
    // ===== DOSEN MANAGEMENT (LEGACY) =====
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/', [AdministratorController::class, 'dosenIndex'])->name('index');
        Route::get('/create', [AdministratorController::class, 'dosenCreate'])->name('create');
        Route::post('/', [AdministratorController::class, 'dosenStore'])->name('store');
        Route::get('/{id}/edit', [AdministratorController::class, 'dosenEdit'])->name('edit');
        Route::put('/{id}', [AdministratorController::class, 'dosenUpdate'])->name('update');
        Route::delete('/{id}', [AdministratorController::class, 'dosenDestroy'])->name('destroy');
    });
    
    // ===== MATA KULIAH MANAGEMENT =====
    Route::prefix('matakuliah')->name('matakuliah.')->group(function () {
        Route::get('/', [AdministratorController::class, 'mataKuliahIndex'])->name('index');
        Route::get('/create', [AdministratorController::class, 'mataKuliahCreate'])->name('create');
        Route::post('/', [AdministratorController::class, 'mataKuliahStore'])->name('store');
        Route::get('/{id}/edit', [AdministratorController::class, 'mataKuliahEdit'])->name('edit');
        Route::put('/{id}', [AdministratorController::class, 'mataKuliahUpdate'])->name('update');
        Route::delete('/{id}', [AdministratorController::class, 'mataKuliahDestroy'])->name('destroy');
    });

    // ✅ MATERI ROUTES (Admin, Dosen, Mahasiswa - Semua bisa akses)
    // Admin & Mahasiswa: View materi
    // Dosen: Kelola materi (create, edit, delete)
    Route::prefix('materi')->name('materi.')->group(function () {
        // View routes - All roles can access
        Route::get('/', [MateriController::class, 'index'])->name('index');
        Route::get('/{id}', [MateriController::class, 'show'])->name('show');
        
        // Update progress - All roles can update their own progress
        Route::post('/{id}/progress', [MateriController::class, 'updateProgress'])->name('updateProgress');
        
        // Management routes - Only Admin & Dosen
        Route::middleware('role:admin,dosen')->group(function () {
            Route::get('/create', [MateriController::class, 'create'])->name('create');
            Route::post('/', [MateriController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MateriController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MateriController::class, 'update'])->name('update');
            Route::delete('/{id}', [MateriController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ✅ JADWAL ROUTES (All roles can access)
    Route::controller(JadwalController::class)->prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/search', 'search')->name('search');
    });
    Route::resource('jadwal', JadwalController::class);
    
    // ✅ TUGAS ROUTES (All roles can access)
    Route::controller(TugasController::class)->prefix('tugas')->name('tugas.')->group(function () {
        // Submit & Grade
        Route::post('/{id}/submit', 'submit')->name('submit');
        Route::post('/{id}/grade', 'grade')->name('grade')->middleware('role:admin,dosen');
        
        // Download Files
        Route::get('/{id}/download-soal', 'downloadSoal')->name('downloadSoal');
        Route::get('/{id}/download-jawaban', 'downloadJawaban')->name('downloadJawaban');
        
        // Export & Filter
        Route::get('/export', 'export')->name('export');
        Route::get('/filter', 'filter')->name('filter');
    });
    Route::resource('tugas', TugasController::class);
    
    // ✅ PENGUMPULAN & REMINDER (All roles)
    Route::resource('pengumpulan', PengumpulanController::class);
    Route::resource('reminder', ReminderController::class);
    
    // ✅ PESERTA PAGE (All roles)
    Route::get('/peserta', function () {
        return view('peserta.index');
    })->name('peserta.index');
    
    // ✅ PENGATURAN PAGE (All roles)
    Route::get('/pengaturan', function () {
        return view('pengaturan.index');
    })->name('pengaturan.index');
    
    // ✅ CHATBOT API ROUTES
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('chat.test');
    });
});