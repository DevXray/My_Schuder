<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::redirect('/', '/dashboard');

// Dashboard (Protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Protected Routes (Butuh Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Materi
    Route::get('/materi', function () {
        return view('materi');
    })->name('materi.index');
    
    // Tugas
    Route::get('/tugas', function () {
        return view('tugas');
    })->name('tugas.index');
    
    // Jadwal
    Route::get('/jadwal', function () {
        return view('jadwal');
    })->name('jadwal.index');
    
    // Peserta
    Route::get('/peserta', function () {
        return view('dashboard'); // Temporary, return dashboard dulu
    })->name('peserta.index');
    
    // Pengaturan
    Route::get('/pengaturan', function () {
        return view('dashboard'); // Temporary, return dashboard dulu
    })->name('pengaturan.index');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Chatbot API Routes
    Route::post('/api/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');
    Route::get('/api/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('chat.test');
});

// Auth Routes (Login, Register, Logout, dll)
require __DIR__.'/auth.php';