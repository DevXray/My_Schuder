<?php
// ============================================
// routes/web.php (UPDATE)
// ============================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SPAController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
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
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes - Redirect root ke dashboard
Route::redirect('/', '/dashboard');

// Auth Routes (Login, Register, Password Reset, etc.)
require __DIR__.'/auth.php';

// Protected Routes (Require Authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ========== DASHBOARD (ALL ROLES) ==========
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management (ALL ROLES)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // ========== ADMINISTRATOR ROUTES (ADMIN ONLY) ==========
    Route::middleware(['role:admin'])->prefix('administrator')->name('administrator.')->group(function () {
        // Dashboard Admin
        Route::get('/', [AdministratorController::class, 'index'])->name('index');
        
        // Mahasiswa CRUD
        Route::get('/mahasiswa', [AdministratorController::class, 'mahasiswaIndex'])->name('mahasiswa.index');
        Route::get('/mahasiswa/create', [AdministratorController::class, 'mahasiswaCreate'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [AdministratorController::class, 'mahasiswaStore'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{id}/edit', [AdministratorController::class, 'mahasiswaEdit'])->name('mahasiswa.edit');
        Route::put('/mahasiswa/{id}', [AdministratorController::class, 'mahasiswaUpdate'])->name('mahasiswa.update');
        Route::delete('/mahasiswa/{id}', [AdministratorController::class, 'mahasiswaDestroy'])->name('mahasiswa.destroy');
        
        // Dosen CRUD
        Route::get('/dosen', [AdministratorController::class, 'dosenIndex'])->name('dosen.index');
        Route::get('/dosen/create', [AdministratorController::class, 'dosenCreate'])->name('dosen.create');
        Route::post('/dosen', [AdministratorController::class, 'dosenStore'])->name('dosen.store');
        Route::get('/dosen/{id}/edit', [AdministratorController::class, 'dosenEdit'])->name('dosen.edit');
        Route::put('/dosen/{id}', [AdministratorController::class, 'dosenUpdate'])->name('dosen.update');
        Route::delete('/dosen/{id}', [AdministratorController::class, 'dosenDestroy'])->name('dosen.destroy');
    });

    // ========== MATA KULIAH (ADMIN & DOSEN) ==========
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::resource('matkul', MatkulController::class);
    });

    // ========== MATERI (VIEW: ALL | CRUD: ADMIN & DOSEN) ==========
    // Mahasiswa hanya bisa view
    Route::middleware(['role:mahasiswa,dosen,admin'])->group(function () {
        Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
        Route::get('/materi/{id}', [MateriController::class, 'show'])->name('materi.show');
        Route::post('/materi/{id}/progress', [MateriController::class, 'updateProgress'])->name('materi.updateProgress');
    });
    
    // Dosen & Admin bisa CRUD
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/materi/create', [MateriController::class, 'create'])->name('materi.create');
        Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
        Route::get('/materi/{id}/edit', [MateriController::class, 'edit'])->name('materi.edit');
        Route::put('/materi/{id}', [MateriController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{id}', [MateriController::class, 'destroy'])->name('materi.destroy');
    });

    // ========== TUGAS ==========
    // Mahasiswa: View, Submit, CRUD own submissions
    Route::middleware(['role:mahasiswa,dosen,admin'])->group(function () {
        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/{id}', [TugasController::class, 'show'])->name('tugas.show');
        
        // Mahasiswa submit tugas
        Route::post('/tugas/{id}/submit', [TugasController::class, 'submit'])->name('tugas.submit');
    });

    // Dosen & Admin: Full CRUD + Grading
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/tugas/{id}/edit', [TugasController::class, 'edit'])->name('tugas.edit');
        Route::put('/tugas/{id}', [TugasController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::post('/tugas/{id}/grade', [TugasController::class, 'grade'])->name('tugas.grade');
    });

    // ========== JADWAL (ALL ROLES CAN VIEW) ==========
    Route::controller(JadwalController::class)->prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'search')->name('search');
    });
    
    // Admin & Dosen bisa CRUD jadwal
    Route::middleware(['role:admin,dosen'])->group(function () {
        Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{id}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    });

    // ========== PENGUMPULAN TUGAS ==========
    Route::resource('pengumpulan', PengumpulanController::class);
    
    // ========== REMINDER ==========
    Route::resource('reminder', ReminderController::class);
    
    // ========== PESERTA (ALL ROLES) ==========
    Route::get('/peserta', function () {
        return view('peserta.index');
    })->name('peserta.index');
    
    // ========== PENGATURAN (ALL ROLES) ==========
    Route::get('/pengaturan', function () {
        return view('dashboard');
    })->name('pengaturan.index');
    
    // ========== CHATBOT API ==========
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('chat.test');
    });
});