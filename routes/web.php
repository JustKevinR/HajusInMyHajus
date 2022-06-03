<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\StoreController;
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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/Weather', [WeatherController::class, 'weather']);

Route::get('/blog', [BlogController::class, 'index']);
Route::post('/comments', [CommentsController::class, 'index']);
Route::post('/comment/{blog}', [CommentsController::class, 'store']);
Route::post('/comment-delete/{comment}', [CommentsController::class, 'delete'])->middleware('role:Admin');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/store', [StoreController::class, 'index'])->name('store');
Route::post('/cart', [StoreController::class, 'cartAdd'])->name('add.cart');
Route::get('/cart', [StoreController::class, 'CartList'])->name('cart.list');
Route::put('/cart/{id}', [StoreController::class, 'updateCart'])->name('update.cart');
Route::delete('/cart/{id}', [StoreController::class, 'destroy'])->name('delete.cart');

Route::delete('/googlemaps/{id}', [MarkerController::class, 'delete'])->name('marker.delete');
Route::get('/googlemaps', [MarkerController::class, 'index']);
Route::post('/googlemaps', [MarkerController::class, 'store']);
Route::get('/googlemaps/marker/{id}', [MarkerController::class, 'show']);
Route::post('/googlemaps/marker/{id}', [MarkerController::class, 'update'])->name("marker.edit");

Route::get('/success', [StoreController::class, 'success']);

require __DIR__.'/auth.php';
