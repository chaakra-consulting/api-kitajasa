<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DetailTransaksiController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\VendorController;
use App\Models\Customer;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Akses tidak diperbolehkan',
        'status_code' => 401
    ], 401);
})->name('login');

Route::post('/auth', [AuthController::class, 'login']);

Route::post('/customer/create', [CustomerController::class, 'create']);
Route::post('/vendor/create', [VendorController::class, 'create']);

Route::middleware(['auth:sanctum'])->group(function () {
    //vendors url
    Route::get('/vendor', [VendorController::class, 'index']);
    Route::put('/vendor/update/{id}', [VendorController::class, 'update']);
    Route::delete('/vendor/delete/{id}', [VendorController::class, 'delete']);
    //customers url
    Route::get('/customer', [CustomerController::class, 'index']);
    Route::put('/customer/update/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/delete/{id}', [CustomerController::class, 'delete']);
    //category url
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category/create', [CategoryController::class, 'create']);
    Route::put('/category/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/delete/{id}', [CategoryController::class, 'delete']);
    //layanan url
    Route::get('/layanan', [LayananController::class, 'index']);
    Route::post('/layanan/create', [LayananController::class, 'create']);
    Route::put('/layanan/update/{id}', [LayananController::class, 'update']);
    Route::delete('/layanan/delete/{id}', [LayananController::class, 'delete']);
    //Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi/create', [TransaksiController::class, 'create']);
    Route::delete('/transaksi/delete/{id}', [TransaksiController::class, 'delete']);
    //detail transaksi url
    Route::get('/detail_transaksi/{id}', [DetailTransaksiController::class, 'index']);
    Route::post('/detail_transaksi/create/{id}', [DetailTransaksiController::class, 'create']);
});
