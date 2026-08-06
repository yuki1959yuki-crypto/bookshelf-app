<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'summary' => [
                'total_reviews' => $user->reviews()->count(),
                'books_read' => $user->reviews()->distinct('book_id')->count('book_id'),
                'average_rating' => $user->reviews()->avg('rating') ?? 0,
            ],
            'rating_distribution' => [],
            'top_rated_books' => [],
            'genre_ratings' => [],
        ];

        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $distributionData = $user->reviews()
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating');

        foreach ($distributionData as $rating => $count) {
            if (isset($ratingDistribution[$rating])) {
                $ratingDistribution[$rating] = $count;
            }
        }
        $stats['rating_distribution'] = $ratingDistribution;

        $stats['top_rated_books'] = $user->reviews()
            ->with('book')
            ->where('rating', '>=', 4)
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $stats['genre_ratings'] = DB::table('reviews')
            ->join('book_genre', 'reviews.book_id', '=', 'book_genre.book_id')
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
            ->where('reviews.user_id', $user->id)
            ->select('genres.id', 'genres.name', DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(reviews.id) as review_count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('avg_rating')
            ->orderByDesc('review_count')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        return view('reports.index', ['stats' => $stats]);
    }
}
