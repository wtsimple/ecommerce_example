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
// Anybody can read
Route::get('/product/', [ProductController::class, 'index']);
Route::get('/product/count', [ProductController::class, 'count']);
Route::get('/product/{product}', [ProductController::class, 'show']);
// CUD requires authentication
Route::prefix('')->middleware('auth:sanctum')->group(function() {
    Route::post('/product', [ProductController::class, 'store']);
    Route::delete('/product/{product}', [ProductController::class, 'destroy']);
    Route::patch('/product/{product}', [ProductController::class, 'update']);
});

// ---- PURCHASES ROUTES -----------------
Route::post('/purchase', [PurchaseController::class, 'buy']);


