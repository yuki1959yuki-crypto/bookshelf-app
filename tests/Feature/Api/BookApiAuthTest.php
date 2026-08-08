<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_book()
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => 'テスト',
            'author' => '著者',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_with_invalid_token_gets_unauthorized()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token-string')
            ->postJson('/api/v1/books', [
                'title' => 'テスト',
                'author' => '著者',
            ]);

        $response->assertUnauthorized();
    }

    public function test_user_with_valid_token_can_create_book()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/books', [
                'title' => '有効なテスト書籍',
                'author' => 'テスト著者',
                'isbn' => '9784873117586',
                'published_date' => '2026-01-01',
            ]);

        $response->assertCreated();
    }

    public function test_user_cannot_update_others_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/v1/books/{$book->id}", [
                'title' => '改ざん',
                'author' => '改ざん作者',
                'isbn' => '9784873117586',
                'published_date' => '2026-01-01',
            ]);

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_others_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->deleteJson("/api/v1/books/{$book->id}");

        $response->assertForbidden();
    }
}
