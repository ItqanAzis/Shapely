<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;

// === PUBLIC ROUTES (Anyone can access) ===
Route::post('/login', [AuthController::class, 'login']);

// === PROTECTED ROUTES (Need Token) ===
// Everything inside this 'group' requires a valid login token
Route::middleware('auth:sanctum')->group(function () {
    
    // Admin Routes (Add/Edit/Delete)
    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
    
    // Logout & User Info
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// === PUBLIC READ-ONLY ===
// We let anyone SEE the items, but they can't touch them
Route::get('/items', [ItemController::class, 'index']);