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

        $response->assertUnauthorized(); // 401
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
            ]);

        $response->assertForbidden(); // 403
    }
}
