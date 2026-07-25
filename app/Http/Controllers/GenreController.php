<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
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

    public function show(Genre $genre)
    {
        $books = $genre->books()
            ->with('genres')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        $validated = $request->validated();

        Genre::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $validated = $request->validated();

        $genre->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('genres.index')->with('success', 'ジャンル名を更新しました。');
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return back()->with('error', '書籍が登録されているジャンルは削除できません。');
        }

        $genre->delete();

        return redirect()->route('genres.index')->with('success', 'ジャンルを削除しました。');
    }
}
