<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/materi', function () {
    return view('materi');
});

Route::get('/tugas', function () {
    return view('tugas');
});

Route::get('/jadwal', function () {
    return view('jadwal');
});

// API Routes
Route::post('/api/chat', [ChatbotController::class, 'sendMessage']);
Route::get('/api/chat/test', [ChatbotController::class, 'testGeminiAPI']);