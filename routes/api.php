<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

//Open routes
Route::post('/register', [AuthController::class,'AddUser']);
Route::post('/login', [AuthController::class, 'login_admin']);

//Protected routes
Route::group(['middleware' => 'jwt.auth'], function () {

    //Products related routes
    Route::post('/Add_product', [ProductController::class, 'AddProduct']);
    Route::get('/edit_product/{id}', [ProductController::class, 'EditProduct']);
    Route::put('/edit_product/{id}', [ProductController::class, 'UpdateProduct']);
    Route::delete('/delete_product/{id}', [ProductController::class, 'DeleteProduct']);
    Route::get('products' , [ArticlesController::class , 'AllProducts']);
    //change password route
    Route::post('/change-password/{id}', [AuthController::class, 'changePassword']);
    //Articles related routes
    Route::post('/add_article' , [ArticlesController::class , 'AddArticle']);
    Route::get('/articles' , [ArticlesController::class , 'AllArticles']);
    Route::delete('/delete_article/{id}' , [ArticlesController::class , 'DeleteArticle']);
    Route::get('/edit_article/{id}' , [ArticlesController::class , 'EditArticle']);
    Route::put('/edit_article/{id}' , [ArticlesController::class , 'UpdateArticle']);

});

