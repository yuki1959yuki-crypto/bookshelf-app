<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_book_show(): void
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}");

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_book_show_with_details(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'comment' => '素晴らしい本です',
            'rating' => 5,
        ]);

        $response = $this->actingAs($user)->get("/books/{$book->id}");

        $response->assertStatus(200);

        $response->assertSee($book->title);

        $response->assertSee($review->comment);

        $response->assertSee($book->average_rating);
    }

    public function test_returns_404_when_book_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/99999');

        $response->assertStatus(404);
    }
}
