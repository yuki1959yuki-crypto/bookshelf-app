<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class)->except(['index', 'show']);

    Route::post('/books/{book}/favorite', function () {
        return back();
    })->name('favorites.toggle');

    Route::post('/books/{book}/reviews', function () {
        return back();
    })->name('reviews.store');

    Route::post('/reviews/{review}/like', function () {
        return back();
    })->name('reviews.like');

    Route::get('/ranking', function () {
        return 'ランキング画面（開発予定）';
    })->name('ranking.index');

    Route::get('/favorites', function () {
        return 'お気に入り画面（開発予定）';
    })->name('favorites.index');

    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');

    Route::get('/genres/create', function () {
        return 'ジャンル登録画面（開発予定）';
    })->name('genres.create');

    Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

    Route::get('/genres/{genre}/edit', function () {
        return 'ジャンル編集画面（開発予定）';
    })->name('genres.edit');
    Route::delete('/genres/{genre}', function () {
        return back();
    })->name('genres.destroy');
});

Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
