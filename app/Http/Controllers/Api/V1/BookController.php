<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookSearchRequest;
use App\Http\Requests\Api\V1\StoreBookApiRequest;
use App\Http\Requests\Api\V1\UpdateBookApiRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(BookSearchRequest $request)
    {
        $query = Book::query()
            ->with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($genreId = $request->input('genre_id')) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        $perPage = $request->input('per_page', 10);
        $books = $query->latest('id')->paginate($perPage);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['genres'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    public function store(StoreBookApiRequest $request)
    {
        $book = DB::transaction(function () use ($request) {
            $data = array_merge($request->validated(), [
                'user_id' => auth()->id() ?? 1,
            ]);

            $book = Book::create($data);

            if ($request->has('genre_ids')) {
                $book->genres()->sync($request->input('genre_ids', []));
            }

            return $book;
        });

        $book->load(['genres'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateBookApiRequest $request, Book $book)
    {
        DB::transaction(function () use ($request, $book) {
            $book->update($request->validated());

            if ($request->has('genre_ids')) {
                $book->genres()->sync($request->input('genre_ids', []));
            }
        });

        $book->load(['genres'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }
}
