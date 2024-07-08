<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'AllProducts']);
Route::get('/Add_product', function () {
    return view('Add_product');
});

Route::post('/Add_product', [ProductController::class, 'AddProduct']);
Route::get('/edit_product/{id}', [ProductController::class, 'EditProduct']);
Route::put('/edit_product/{id}', [ProductController::class, 'UpdateProduct']);
Route::delete('/delete_product/{id}', [ProductController::class, 'DeleteProduct']);
