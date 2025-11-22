<?php

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
    
    // Mahasiswa & Dosen Management
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::resource('dosen', DosenController::class);
    
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