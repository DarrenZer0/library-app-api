<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;

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


Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('books', [BookController::class, 'index']);


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('books/records', [BorrowController::class, 'index']);
    Route::post('books', [BookController::class, 'add_books']);
    Route::put('books/update/{id}', [BookController::class, 'edit']);
    Route::delete('books/delete/{id}', [BookController::class, 'delete']);
    Route::put('borrows/{userId}/{borrowId}/status', [BorrowController::class, 'updateStatus']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('books/borrow/{id}', [BorrowController::class, 'borrow']);
    Route::get('books/user_records', [BorrowController::class, 'user_index']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
