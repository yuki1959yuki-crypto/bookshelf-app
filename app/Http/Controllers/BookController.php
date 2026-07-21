<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;

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
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $book = Auth::user()->books()->create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (isset($validated['genres'])) {
            $book->genres()->sync($validated['genres']);
        }

        return redirect()->route('books.index')->with('success', '書籍を登録しました。');
    }

    public function show(Book $book)
    {
        $book->load(['genres', 'reviews.user', 'reviews.likedByUsers']);

        $reviewsAvg = $book->reviews()->avg('rating');

        return view('books.show', compact('book', 'reviewsAvg'));
    }
}
