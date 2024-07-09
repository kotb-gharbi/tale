<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

//Open routes
Route::post('/register', [AuthController::class,'Register_to_backoffice']);
Route::post('/login', [AuthController::class, 'login_admin']);

//Protected routs
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('/Add_product', [ProductController::class, 'AddProduct']);
    Route::post('/change-password/{id}', [AuthController::class, 'changePassword']);
    Route::get('/edit_product/{id}', [ProductController::class, 'EditProduct']);
    Route::put('/edit_product/{id}', [ProductController::class, 'UpdateProduct']);
    Route::delete('/delete_product/{id}', [ProductController::class, 'DeleteProduct']);
});

