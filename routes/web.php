<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\URL;

Route::get('/', function () {
    return Inertia::render('Home', []);
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('product');
Route::get('/products/{productId}', [ProductController::class, 'show'])->name('product.show');
Route::post('/products', [ProductController::class, 'store'])->name('product.create');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.delete');


Route::get('/customers', [CustomerController::class, 'index'])->name('customer');
Route::get('/customers/{customerId}', [CustomerController::class, 'show'])->name('customer.show');
Route::post('/customers', [CustomerController::class, 'store'])->name('customer.create');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customer.delete');

Route::get('/orders', [OrderController::class, 'index'])->name('order');
Route::post('/orders', [OrderController::class, 'store'])->name('order.create');
Route::put('/orders/{order}', [OrderController::class, 'update'])->name('order.update');
Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('order.delete');

Route::get('/load-customers', [CustomerController::class, 'loadCustomersCursorBased'])->name('customer.loadByCursor');
Route::get('/load-products', [ProductController::class, 'loadProductsCursorBased'])->name('product.loadByCursor');


Route::get('/not-found', function () {
    return Inertia::render('NotFound');
})->name('not_found');
