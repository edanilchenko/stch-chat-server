<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;

Route::get('/messages', [ChatController::class, 'index']);
Route::post('/messages', [ChatController::class, 'store']);