<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

// Test route dulu
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

Route::post('/chat', [ChatbotController::class, 'sendMessage']);
Route::get('/chat/test', [ChatbotController::class, 'testGeminiAPI']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});