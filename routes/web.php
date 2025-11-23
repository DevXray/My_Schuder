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
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // ✅ ADMINISTRATOR ROUTES (Only Admin)
    Route::prefix('administrator')->name('administrator.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdministratorController::class, 'index'])->name('index');
        
        // Mahasiswa Management
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/', [AdministratorController::class, 'mahasiswaIndex'])->name('index');
            Route::get('/create', [AdministratorController::class, 'mahasiswaCreate'])->name('create');
            Route::post('/', [AdministratorController::class, 'mahasiswaStore'])->name('store');
            Route::get('/{id}/edit', [AdministratorController::class, 'mahasiswaEdit'])->name('edit');
            Route::put('/{id}', [AdministratorController::class, 'mahasiswaUpdate'])->name('update');
            Route::delete('/{id}', [AdministratorController::class, 'mahasiswaDestroy'])->name('destroy');
        });
        
        // Dosen Management
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', [AdministratorController::class, 'dosenIndex'])->name('index');
            Route::get('/create', [AdministratorController::class, 'dosenCreate'])->name('create');
            Route::post('/', [AdministratorController::class, 'dosenStore'])->name('store');
            Route::get('/{id}/edit', [AdministratorController::class, 'dosenEdit'])->name('edit');
            Route::put('/{id}', [AdministratorController::class, 'dosenUpdate'])->name('update');
            Route::delete('/{id}', [AdministratorController::class, 'dosenDestroy'])->name('destroy');
        });
        
        // Mata Kuliah Management
        Route::prefix('matakuliah')->name('matakuliah.')->group(function () {
            Route::get('/', [AdministratorController::class, 'mataKuliahIndex'])->name('index');
            Route::get('/create', [AdministratorController::class, 'mataKuliahCreate'])->name('create');
            Route::post('/', [AdministratorController::class, 'mataKuliahStore'])->name('store');
            Route::get('/{id}/edit', [AdministratorController::class, 'mataKuliahEdit'])->name('edit');
            Route::put('/{id}', [AdministratorController::class, 'mataKuliahUpdate'])->name('update');
            Route::delete('/{id}', [AdministratorController::class, 'mataKuliahDestroy'])->name('destroy');
        });
    });
    
    // Jadwal (Schedule) Management
    Route::controller(JadwalController::class)->prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/search', 'search')->name('search');
    });
    Route::resource('jadwal', JadwalController::class);
    
    // Materi (Course Materials)
    Route::post('/materi/{id}/progress', [MateriController::class, 'updateProgress'])
        ->name('materi.updateProgress');
    
    Route::resource('materi', MateriController::class);
    
    // Tugas (Assignments) & Pengumpulan (Submissions)
    Route::controller(TugasController::class)->prefix('tugas')->name('tugas.')->group(function () {
        // Submit & Grade
        Route::post('/{id}/submit', 'submit')->name('submit');
        Route::post('/{id}/grade', 'grade')->name('grade');
        
        // Download Files
        Route::get('/{id}/download-soal', 'downloadSoal')->name('downloadSoal');
        Route::get('/{id}/download-jawaban', 'downloadJawaban')->name('downloadJawaban');
        
        // Export & Filter
        Route::get('/export', 'export')->name('export');
        Route::get('/filter', 'filter')->name('filter');
    });
    Route::resource('tugas', TugasController::class);
    Route::resource('pengumpulan', PengumpulanController::class);
    
    // Reminder Management
    Route::resource('reminder', ReminderController::class);
    
    // Additional Pages (Temporary views)
    Route::get('/peserta', function () {
        return view('peserta.index'); // TODO: Create peserta view
    })->name('peserta.index');
    
    Route::get('/pengaturan', function () {
        return view('dashboard'); // TODO: Create pengaturan view
    })->name('pengaturan.index');
    
    // Chatbot API Routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('chat.test');
    });
});