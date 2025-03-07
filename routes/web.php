<?php

use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\WebAuthController;
use  App\Http\Controllers\ProductController;
use  App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('swiper', function(){
    return view('swiper');
});

Route::get('/login',function(){
    return view('login');
})->name('admin.login');

Route::post('/login', [WebAuthController::class, 'login'])->name('login');

Route::get('/register', function(){
    return view('register');
})->name('admin.register');

Route::post('/register', [WebAuthController::class, 'register'])->name('register');

Route::get('/index',function(){
    return view('index');
})->name('admin.index');

Route::get('/index2', function(){
    return view('index2');
})->name('admin.index2');

Route::post('/forgot-password/check-email', [WebAuthController::class, 'checkEmail'])->name('forgot.password.checkEmail');

Route::get('/reset-password', [WebAuthController::class, 'showResetForm'])->name('password.reset');

Route::post('/reset-password', [WebAuthController::class, 'updatePassword'])->name('password.update');

Route::middleware('auth','throttle:10,1')->group(function(){
    Route::get('/index3',[WebAuthController::class, 'dashboard'])->name('admin.index3');
    Route::get('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [WebAuthController::class, 'changePassword'])->name('change-password');
    Route::post('/update-profile', [WebAuthController::class, 'updateProfile'])->name('updateProfile');

    Route::get('/products', [ProductController::class,'index'])->name('products.index');
    Route::get('/add-products', [ProductController::class, 'addProduct'])->name('products.add');
    Route::post('/add-products', [ProductController::class, 'storeProduct'])->name('products.store');
    Route::get('/show-products', [ProductController::class, 'showProducts'])->name('products.show');
    Route::get('/edit-products/{id}', [ProductController::class, 'editProduct'])->name('products.edit');
    Route::post('/update-products', [ProductController::class, 'updateProduct'])->name('products.update');
    Route::delete('/delete-products/{id}', [ProductController::class, 'deleteProduct'])->name('products.delete');
    Route::get('/cart/add/{id}', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [ProductController::class, 'showCart'])->name('cart.index');
    Route::delete('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/buy-product/{id}', [ProductController::class, 'buyProduct'])->name('product.buy');
    Route::post('/makePayment', [ProductController::class, 'makePayment'])->name('product.payment');
    Route::get('/successfullPayment', [ProductController::class, 'successfullPayment'])->name('product.successfullPayment');
    Route::get('/cancelledPayment', [ProductController::class, 'cancelledPayment'])->name('product.cancelledPayment');
    Route::post('/cart/makePayment', [ProductController::class, 'cartPayment'])->name('product.cart.payment');
    Route::get('/successfullCartPayment', [ProductController::class, 'cartSuccessfulPayment'])->name('product.cart.successfullPayment');
    Route::get('/cancelledCartPayment', [ProductController::class, 'cartCancelledPayment'])->name('product.cart.cancelledPayment');
    Route::get('/my-orders', [ProductController::class, 'myOrders'])->name('products.myOrders');
    Route::post('/refund/{id}', [ProductController::class, 'refund'])->name('products.refund');
    Route::post('/update-cart-quantity', [ProductController::class, 'updateQuantity'])->name('cart.updateQuantity');


    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/add-invoice', [InvoiceController::class, 'addInvoice'])->name('invoice.add');
    Route::post('/store-invoice', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/show-invoice',  [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/edit-invoice/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('/update-invoice/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::post('/delete-invoice/{id}', [InvoiceController::class, 'delete'])->name('invoice.delete');
    Route::get('/invoice/{id}',  [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
    Route::get('/invoice/export/{id}',  [InvoiceController::class, 'exportAsCsv'])->name('invoice.export');
});
Route::get('/image', [ImageController::class, 'index'])->name('image.index');
Route::post('/image-store', [ImageController::class, 'storeImage'])->name('image.store');
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/fetch', [CategoryController::class,'fetchCategories'])->name('categories.fetch');
Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::post('/categories/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');
Route::get('/categories/download',  [CategoryController::class, 'download'])->name('categories.download');

Route::get('user-notify', [ProductController::class, 'notifyUser']);