<?php

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\api\UserRoleController;
use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportSalesController;
use App\Http\Controllers\Api\SalesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api;" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/roles', [UserRoleController::class, 'index']);
    Route::get('/roles/{id}', [UserRoleController::class, 'show']);
    Route::post('/roles', [UserRoleController::class, 'store']);
    Route::put('/roles', [UserRoleController::class, 'update']);
    Route::delete('/roles/{id}', [UserRoleController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/categories', [ProductCategoryController::class, 'index']);
    Route::get('/categories/{id}', [ProductCategoryController::class, 'show']);
    Route::post('/categories', [ProductCategoryController::class, 'store']);
    Route::put('/categories', [ProductCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [ProductCategoryController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/sales', [SalesController::class, 'index']);
    Route::get('/sales/{id}', [SalesController::class, 'show']);
    Route::post('/sales', [SalesController::class, 'store']);
    Route::get('/sale', [SalesController::class, 'viewSales']);
    // Route::put('/sales', [SalesController::class, 'update']);
    // Route::delete('/sales/{id}', [SalesController::class, 'destroy']);
});

Route::get('/report/sales-menu', [ReportSalesController::class, 'viewSalesCategories']);
// Route::get('/v1/sale-customer', [ReportSalesController::class, 'viewSalesCustomers']);
Route::prefix('v1')->group(function () {
    Route::get('/sale-customer', [ReportSalesController::class, 'viewSalesCustomers']);
});

Route::get('/', function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});

/**
 * Jika Frontend meminta request endpoint API yang tidak terdaftar
 * maka akan menampilkan HTTP 404
 */
Route::fallback(function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});
