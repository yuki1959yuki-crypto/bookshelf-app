<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCrudAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_book(): void
    {
        $user = User::factory()->create();

        $genre = Genre::firstOrCreate(['name' => 'テスト用ジャンル']);

        $bookData = [
            'title' => 'テスト用書籍名',
            'author' => 'テスト著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
            'description' => '詳細説明テキストです。',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->post('/books', $bookData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/books');

        $this->assertDatabaseHas('books', [
            'title' => 'テスト用書籍名',
            'user_id' => $user->id,
        ]);
    }
}
