<?php

namespace Tests\Feature\Books;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_create(): void
    {
        $response = $this->get('/books/create');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_create_screen(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/books/create');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_a_book(): void
    {
        $user = User::factory()->create();

        $genre = Genre::create(['name' => '技術書']);

        $response = $this->actingAs($user)->post('/books', [
            'title' => 'テスト書籍タイトル',
            'author' => 'テスト著者名',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id], `genres`,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseHas('books', [
            'title' => 'テスト書籍タイトル',
            'user_id' => $user->id,
        ]);
    }

    public function test_validation_errors_for_invalid_inputs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/books', [
            'title' => '',
            'author' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['title', 'author']);
    }
}
