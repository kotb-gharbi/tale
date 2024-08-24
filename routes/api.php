<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

//Open routes
Route::post('/login', [AuthController::class, 'SuperAdminLogin'])->middleware('json');
Route::get('/getUsers' , [AuthController::class , 'GetAllUsers'])->middleware('json');



//Protected routes
Route::group(['middleware' => 'jwt.auth'], function () {
    //Admin routes
    Route::post('/register', [AuthController::class,'AddUser'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::post('/edit-roles/{id}', [AuthController::class,'EditRoles'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::get('/getUser/{id}' , [AuthController::class , 'GetUser'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::get('/edit-status/{id}' , [AuthController::class , 'EditStatus'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-name/{id}' ,[AuthController::class , 'EditName'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-lastname/{id}' ,[AuthController::class , 'EditLastName'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-birth/{id}' ,[AuthController::class , 'EditBirthDate'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-gender/{id}' ,[AuthController::class , 'EditGender'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-email/{id}' ,[AuthController::class , 'EditEmail'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-country/{id}' ,[AuthController::class , 'EditCountry'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-tel/{id}' ,[AuthController::class , 'EditTel'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-address/{id}' ,[AuthController::class , 'EditAddress'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/edit-codepostal/{id}' ,[AuthController::class , 'EditCodePostal'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::put('/change-password/{id}', [AuthController::class, 'ChangePassword'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::delete('/delete_user/{id}', [AuthController::class,'DeleteUser'])->middleware(['json' , 'roles:admin,super_admin' ]);
    Route::get('/AllRoles', [AuthController::class, 'AllRoles'])->middleware(['json' , 'roles:admin,super_admin' ]);
    //Products related routes
    Route::post('/product/add', [ProductController::class, 'AddProduct'])->middleware('roles:admin,editor');
    Route::get('/product/{id}/edit', [ProductController::class, 'EditProduct'])->middleware('roles:admin,editor');
    Route::post('/product/{id}/edit', [ProductController::class, 'UpdateProduct'])->middleware('roles:admin,editor');
    Route::delete('/product/{id}/delete', [ProductController::class, 'DeleteProduct'])->middleware('roles:admin,editor');
    Route::get('products' , [ProductController::class , 'AllProducts'])->middleware('roles:admin');
    
    //Articles related routes
    Route::post('/article/add' , [ArticlesController::class , 'AddArticle'])->middleware('roles:admin,editor');
    Route::get('/articles' , [ArticlesController::class , 'AllArticles'])->middleware('roles:admin');
    Route::delete('/article/{id}/delete' , [ArticlesController::class , 'DeleteArticle'])->middleware('roles:admin,editor');
    Route::get('/article/{id}/edit' , [ArticlesController::class , 'EditArticle'])->middleware('roles:admin,editor');
    Route::post('/article/{id}/edit' , [ArticlesController::class , 'UpdateArticle'])->middleware('roles:admin,editor');

});
