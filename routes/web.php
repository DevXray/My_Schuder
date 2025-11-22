<?php
// ============================================
// routes/web.php (FIXED VERSION)
// ============================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\MatkulController;

/*
|--------------------------------------------------------------------------
| Web Routes - FIXED
|--------------------------------------------------------------------------
*/

// Public Routes
Route::redirect('/', '/dashboard');

// Auth Routes
require __DIR__.'/auth.php';

// ========== AUTHENTICATED ROUTES ==========
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ===== DASHBOARD (ALL ROLES) =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== PROFILE (ALL ROLES) =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // ===== ADMINISTRATOR ROUTES (ADMIN ONLY) =====
    Route::middleware(['role:admin'])->prefix('administrator')->name('administrator.')->group(function () {
        Route::get('/', [AdministratorController::class, 'index'])->name('index');
        
        // Mahasiswa CRUD
        Route::resource('mahasiswa', AdministratorController::class)
            ->parameters(['mahasiswa' => 'id'])
            ->names([
                'index' => 'mahasiswa.index',
                'create' => 'mahasiswa.create',
                'store' => 'mahasiswa.store',
                'edit' => 'mahasiswa.edit',
                'update' => 'mahasiswa.update',
                'destroy' => 'mahasiswa.destroy',
            ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        
        // Dosen CRUD
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', [AdministratorController::class, 'dosenIndex'])->name('index');
            Route::get('/create', [AdministratorController::class, 'dosenCreate'])->name('create');
            Route::post('/', [AdministratorController::class, 'dosenStore'])->name('store');
            Route::get('/{id}/edit', [AdministratorController::class, 'dosenEdit'])->name('edit');
            Route::put('/{id}', [AdministratorController::class, 'dosenUpdate'])->name('update');
            Route::delete('/{id}', [AdministratorController::class, 'dosenDestroy'])->name('destroy');
        });
    });

    // ===== MATA KULIAH (ADMIN & DOSEN) =====
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::resource('matkul', MatkulController::class);
    });

    // ===== MATERI ROUTES (FIXED) =====
    // ✅ Semua role bisa VIEW
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{id}', [MateriController::class, 'show'])->name('materi.show');
    Route::post('/materi/{id}/progress', [MateriController::class, 'updateProgress'])->name('materi.updateProgress');
    
    // ✅ Hanya Admin & Dosen bisa CRUD
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/materi/create', [MateriController::class, 'create'])->name('materi.create');
        Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
        Route::get('/materi/{id}/edit', [MateriController::class, 'edit'])->name('materi.edit');
        Route::put('/materi/{id}', [MateriController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{id}', [MateriController::class, 'destroy'])->name('materi.destroy');
    });

    // ===== TUGAS ROUTES (FIXED) =====
    // ✅ Semua role bisa VIEW
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [TugasController::class, 'show'])->name('tugas.show');
    
    // ✅ Mahasiswa bisa submit
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::post('/tugas/{id}/submit', [TugasController::class, 'submit'])->name('tugas.submit');
    });

    // ✅ Admin & Dosen: Full CRUD + Grading
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/tugas/{id}/edit', [TugasController::class, 'edit'])->name('tugas.edit');
        Route::put('/tugas/{id}', [TugasController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::post('/tugas/{id}/grade', [TugasController::class, 'grade'])->name('tugas.grade');
    });

    // ===== JADWAL (ALL ROLES) =====
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/search', [JadwalController::class, 'search'])->name('jadwal.search');
    
    // ✅ Admin & Dosen bisa CRUD
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{id}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    });

    // ===== PENGUMPULAN & REMINDER =====
    Route::resource('pengumpulan', PengumpulanController::class);
    Route::resource('reminder', ReminderController::class);
    
    // ===== PESERTA & PENGATURAN =====
    Route::get('/peserta', fn() => view('peserta.index'))->name('peserta.index');
    Route::get('/pengaturan', fn() => view('dashboard'))->name('pengaturan.index');
    
    // ===== CHATBOT API =====
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('chat.test');
    });
});