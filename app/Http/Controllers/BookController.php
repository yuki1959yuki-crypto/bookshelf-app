<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Book $book)
    {
        $book->load(['genres', 'reviews.user', 'reviews.likedByUsers']);

        $reviewsAvg = $book->reviews()->avg('rating');

        return view('books.show', compact('book', 'reviewsAvg'));
    }
}
