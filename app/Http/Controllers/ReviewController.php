<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book)
    {
        $validated = $request->validated();

        Review::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('books.show', $book)->with('success', 'レビューを投稿しました。');
    }
}
