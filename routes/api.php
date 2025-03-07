<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\ApiCategoryController;
use App\Http\Controllers\ApiInvoiceController;

Route::post('auth/login',[ApiAuthController::class, 'login']);
Route::post('auth/register', [ApiAuthController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('auth/user', [ApiAuthController::class,  'user']);
    Route::get('auth/logout', [ApiAuthController::class, 'logout']);
    Route::post('auth/change-password', [ApiAuthController::class, 'changePassword']);
    Route::post('auth/update-profile', [ApiAuthController::class,'updateProfile']);
    Route::post('add-products', [ApiProductController::class, 'addProduct']);
    Route::get('show-products', [ApiProductController::class, 'listProducts']);
    Route::post('show-products', [ApiProductController::class, 'showProduct']);
    Route::post('update-products', [ApiProductController::class, 'updateProduct']);
    Route::post('delete-products', [ApiProductController::class, 'deleteProduct']);
});

Route::post('categories/create',[ApiCategoryController::class, 'create']);
Route::get('categories/all', [ApiCategoryController::class, 'fetchAll']);
Route::get('categories/show',  [ApiCategoryController::class, 'show']); //Pass category id( for example ?q=7)
Route::post('categories/update', [ApiCategoryController::class, 'update']); //Pass category id( for example ?q=7)
Route::delete('categories/delete', [ApiCategoryController::class, 'destroy']); //Pass category id( for example ?q=7)

Route::post('invoice/create', [ApiInvoiceController::class, 'create']);
Route::get('invoice/all',  [ApiInvoiceController::class, 'fetchAll']);
Route::get('invoice/show',  [ApiInvoiceController::class, 'showInvoice']); //Pass invoice id( for example ?q=7)
Route::post('invoice/update', [ApiInvoiceController::class, 'updateInvoice']); //Pass invoice id( for example ?q=7)
Route::delete('invoice/delete', [ApiInvoiceController::class, 'destroyInvoice']); //Pass invoice id( for example ?q=7)