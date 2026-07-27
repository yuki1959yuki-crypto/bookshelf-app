<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setup(): void
    {
        parent::setUp();

        User::factory()->create(['id' => 1]);
    }

    public function test_api_can_fetch_book_list(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'isbn', 'published_date'],
                ],
            ]);
    }

    public function test_api_can_store_book(): void
    {
        $data = [
            'title' => 'API新規書籍',
            'author' => 'API著者',
            'isbn' => '9784999999999',
            'published_date' => '2026-04-01',
            'description' => 'API経由の登録テスト',
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'API新規書籍');

        $this->assertDatabaseHas('books', ['isbn' => '9784999999999']);
    }

    public function test_api_returns_422_with_japanese_validation_error(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn', 'published_date']);
    }

    public function test_api_can_delete_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);

        $this->getJson("/api/v1/books/{$book->id}")->assertStatus(404);
    }
}
