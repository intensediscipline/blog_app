<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

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

Route::get('/', [UserController::class, "showCorrectHome"])->name('index');
Route::post('/register', [UserController::class, "register"])->name('register');
Route::post('/login', [UserController::class, "login"])->name('login');
Route::post('/logout', [UserController::class, "logout"])->name('logout');

// post related routes
Route::get('/create-post', [PostController::class, "showCreateForm"])->name('create-post');
Route::post('/create-post', [PostController::class, "storeNewPost"])->name('store-new-post');