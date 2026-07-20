<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('books', BookController::class);

Route::get('/ranking', function () {
    return 'ランキング画面（開発予定）';
})->name('ranking.index');

Route::get('/favorites', function () {
    return 'お気に入り画面（開発予定）';
})->name('favorites.index');

Route::get('/genres', function () {
    return 'ジャンル管理画面（開発予定）';
})->name('genres.index');
