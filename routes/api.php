<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->group(function () {
    Route::get('user/{id?}', [UserController::class, 'show']);
    Route::put('user/update/', [UserController::class, 'update']);
    Route::delete('user/delete/', [UserController::class, 'destroy']);

    Route::get('books', [BookController::class, 'index']);
    Route::get('books/user/{id?}', [BookController::class, 'showAllAuthor']);
    Route::get('books/{id}', [BookController::class, 'show']);
    Route::post('books/create', [BookController::class, 'store']);
    Route::put('books/update/{id}', [BookController::class, 'update']);
    Route::delete('books/delete/{id}', [BookController::class, 'destroy']);
});

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
