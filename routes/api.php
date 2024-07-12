<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

//Open routes

Route::post('/login', [AuthController::class, 'login_admin']);

//Protected routes
Route::group(['middleware' => 'jwt.auth'], function () {
    //Admin routes
    Route::post('/register', [AuthController::class,'AddUser'])->middleware('roles:admin');
    Route::post('/edit-roles/{id}', [AuthController::class,'EditRoles'])->middleware('roles:admin');
    Route::delete('/delete_user/{id}', [AuthController::class,'DeleteUser'])->middleware('roles:admin');
    Route::delete('/deactivate_user/{id}', [AuthController::class,'DeactivateUser'])->middleware('roles:admin');
    //Products related routes
    Route::post('/product/add', [ProductController::class, 'AddProduct'])->middleware('roles:admin,editor');
    Route::get('/product/{id}/edit', [ProductController::class, 'EditProduct'])->middleware('roles:admin,editor');
    Route::post('/product/{id}/edit', [ProductController::class, 'UpdateProduct'])->middleware('roles:admin,editor');
    Route::delete('/product/{id}/delete', [ProductController::class, 'DeleteProduct'])->middleware('roles:admin,editor');
    Route::get('products' , [ProductController::class , 'AllProducts'])->middleware('roles:admin');
    //change password route
    Route::post('/change-password/{id}', [AuthController::class, 'changePassword']);
    //Articles related routes
    Route::post('/article/add' , [ArticlesController::class , 'AddArticle'])->middleware('roles:admin,editor');
    Route::get('/articles' , [ArticlesController::class , 'AllArticles'])->middleware('roles:admin');
    Route::delete('/article/{id}/delete' , [ArticlesController::class , 'DeleteArticle'])->middleware('roles:admin,editor');
    Route::get('/article/{id}/edit' , [ArticlesController::class , 'EditArticle'])->middleware('roles:admin,editor');
    Route::post('/article/{id}/edit' , [ArticlesController::class , 'UpdateArticle'])->middleware('roles:admin,editor');

});
