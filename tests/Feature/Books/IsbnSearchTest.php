<?php

namespace Tests\Feature\Books;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_book_by_isbn_with_mocked_api(): void
    {
        $user = User::factory()->create();

        // 外部APIのURLパターンに正確にマッチさせる
        Http::fake([
            'googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 1,
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'テスト書籍タイトル',
                            'authors' => ['テスト著者'],
                            'publishedDate' => '2026-01-01',
                            'description' => 'テスト用の書籍説明文です。',
                            'imageLinks' => [
                                'thumbnail' => 'https://example.com/thumbnail.jpg',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/books/isbn/9784873117586');

        $response->assertOk()
            ->assertJson([
                'title' => 'テスト書籍タイトル',
                'author' => 'テスト著者',
                'published_date' => '2026-01-01',
            ]);
    }

    public function test_handles_external_api_error_or_not_found_gracefully(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 0,
            ], 200),
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/books/isbn/0000000000000');

        $response->assertStatus(404)
            ->assertJsonFragment([
                'error' => '該当する書籍が見つかりませんでした',
            ]);
    }
}
