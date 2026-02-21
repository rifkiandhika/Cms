<?php

use App\Http\Controllers\api\ApiController;
use App\Http\Controllers\api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('produk')->name('api.produk.')->group(function () {
    Route::get('/search', [ProductApiController::class, 'search'])->name('search');
    Route::get('/{id}', [ProductApiController::class, 'show'])->name('show');
});

Route::get('/supplier/{id}/search-products', [ApiController::class, 'searchSupplierProducts'])->name('api.search.products');

