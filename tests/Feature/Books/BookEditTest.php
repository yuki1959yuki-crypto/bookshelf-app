<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_edit(): void
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_owner_can_access_edit_screen(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/books/{$book->id}/edit");

        $response->assertStatus(200);
    }

    public function test_other_user_cannot_access_edit_screen(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get("/books/{$book->id}/edit");

        $response->assertStatus(403);
    }

    public function test_owner_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create([
            'title' => '変更前タイトル',
        ]);
        $genre = Genre::create(['name' => '技術書']);

        $response = $this->actingAs($user)->put("/books/{$book->id}", [
            'title' => '変更後タイトル',
            'author' => '変更後著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '変更後タイトル',
        ]);
    }

    public function test_returns_404_for_non_existent_book(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/99999/edit');

        $response->assertStatus(404);
    }

    public function test_owner_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/books/{$book->id}");

        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
