<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')
            ->orderBy('books_count', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return view('genres.index', compact('genres'));
    }
}
