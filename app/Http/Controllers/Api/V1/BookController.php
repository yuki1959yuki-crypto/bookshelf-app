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
use Illuminate\Support\Facades\Http;

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

        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $perPage = $request->input('per_page', 10);
        $books = $query->paginate($perPage);

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
                'user_id' => $request->user()->id,
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
        $this->authorize('update', $book);

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
        $this->authorize('delete', $book);

        $book->delete();

        return response()->noContent();
    }

    public function searchByIsbn($isbn)
    {
        $apiKey = env('GOOGLE_BOOKS_API_KEY');

        $response = Http::withoutVerifying()->get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'isbn:'.$isbn,
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'APIエラー: '.$response->body()], 500);
        }

        $data = $response->json();
        if (empty($data['totalItems'])) {
            return response()->json(['error' => '該当する書籍が見つかりませんでした'], 404);
        }

        $book = $data['items'][0]['volumeInfo'];

        return response()->json([
            'title' => $book['title'] ?? '',
            'author' => implode(', ', $book['authors'] ?? []) ?: '',
            'published_date' => $book['publishedDate'] ?? '',
            'description' => $book['description'] ?? '',
            'image_url' => $book['imageLinks']['thumbnail'] ?? '',
        ]);
    }
}
