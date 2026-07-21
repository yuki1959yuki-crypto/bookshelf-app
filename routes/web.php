<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class)->except(['index']);

    Route::get('/ranking', function () {
        return 'ランキング画面（開発予定）';
    })->name('ranking.index');

    Route::get('/favorites', function () {
        return 'お気に入り画面（開発予定）';
    })->name('favorites.index');

    Route::get('/genres', function () {
        return 'ジャンル管理画面（開発予定）';
    })->name('genres.index');
});
