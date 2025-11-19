<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\ReminderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama - redirect ke jadwal
Route::get('/', function () {
    return redirect()->route('jadwal.index');
});

// Resource routes untuk Mahasiswa & Dosen
Route::resource('mahasiswa', MahasiswaController::class);
Route::resource('dosen', DosenController::class);

// Routes untuk Jadwal
Route::get('/jadwal/search', [JadwalController::class, 'search'])->name('jadwal.search');
Route::resource('jadwal', JadwalController::class);

// Routes untuk Materi
Route::resource('materi', MateriController::class);

// Routes untuk Tugas & Pengumpulan
Route::resource('tugas', TugasController::class);
Route::resource('pengumpulan', PengumpulanController::class);
Route::post('tugas/{id}/submit', [TugasController::class, 'submit'])->name('tugas.submit');
Route::post('tugas/{id}/grade', [TugasController::class, 'grade'])->name('tugas.grade');

// Routes untuk Reminder
Route::resource('reminder', ReminderController::class);

// API routes untuk Chatbot
Route::prefix('api')->group(function () {
    Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('api.chat.send');
    Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI'])->name('api.chat.test');
});