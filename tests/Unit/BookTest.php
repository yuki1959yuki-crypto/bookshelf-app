<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function book_belongs_to_user()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $book->user->id);
    }

    /** @test */
    public function book_belongs_to_genre()
    {
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'genre_id' => $genre->id,
        ]);

        $this->assertEquals($genre->id, $book->genre->id);
    }

    /** @test */
    public function book_has_many_reviews()
    {
        $book = Book::factory()->create();

        Review::factory()->count(3)->create([
            'book_id' => $book->id,
        ]);

        $this->assertCount(3, $book->reviews);
    }

    /** @test */
    public function book_has_many_favorites()
    {
        $book = Book::factory()->create();

        Favorite::factory()->count(2)->create([
            'book_id' => $book->id,
        ]);

        $this->assertCount(2, $book->favorites);
    }

    /** @test */
    public function book_calculates_average_rating_correctly()
    {
        $book = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $book->id,
            'rating' => 3,
        ]);

        Review::factory()->create([
            'book_id' => $book->id,
            'rating' => 5,
        ]);

        $this->assertEquals(4.0, $book->average_rating);
    }
}
