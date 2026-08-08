<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_books_index(): void
    {
        $response = $this->get('/books');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_books_index(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/books');

        $response->assertStatus(200);
        $response->assertSee($book->title);
        $response->assertSee($book->author);
    }

    public function test_displays_message_when_no_books_exist(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/books');

        $response->assertStatus(200);

    }

    public function test_pagination_works_correctly(): void
    {
        $user = User::factory()->create();

        Book::factory()->count(16)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/books?page=2');

        $response->assertStatus(200);
    }
}
