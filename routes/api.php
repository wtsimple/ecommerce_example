<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// --- PRODUCTS ROUTES -------------
// Unauthenticated
Route::get('/product/', [ProductController::class, 'index']);
Route::get('/product/count', [ProductController::class, 'count']);
Route::get('/product/sku/{product}', [ProductController::class, 'show']);
// Authenticated
Route::prefix('')->middleware('auth:sanctum')->group(function() {
    Route::post('/product', [ProductController::class, 'store']);
    Route::delete('/product/sku/{product}', [ProductController::class, 'destroy']);
    Route::patch('/product/sku/{product}', [ProductController::class, 'update']);
});

// ---- PURCHASES ROUTES -----------------
// authenticated
Route::prefix('')->middleware('auth:sanctum')->group(function () {
    Route::post('/purchase', [PurchaseController::class, 'buy']);
    Route::get('/purchase', [PurchaseController::class, 'index']);
    Route::get('/purchase/revenue', [PurchaseController::class, 'revenue']);
});



