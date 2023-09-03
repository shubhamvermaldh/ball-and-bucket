<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BucketController;
use App\Http\Controllers\BallController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BucketController::class, 'home'])->name('home');
Route::post('/suggest', [BucketController::class, 'suggest'])->name('suggest');


Route::resource('buckets', BucketController::class);
Route::resource('balls', BallController::class);