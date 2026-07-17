<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($books->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $favoriteCount = fake()->numberBetween(3, min(5, $books->count()));
            $favoriteBooks = $books->random($favoriteCount);
            $user->favoriteBooks()->syncWithoutDetaching($favoriteBooks->pluck('id')->toArray());

        }
    }
}
