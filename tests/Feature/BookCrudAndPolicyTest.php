<?php

namespace Tests\Feature;

use App\Models\Book;
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

        $response->assertRedirect();

        $this->assertDatabaseHas('books', [
            'title' => 'テスト用書籍名',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_read_book_list_and_detail(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $this->actingAs($user)->get('/books')
            ->assertStatus(200)
            ->assertSee($book->title);

        $this->actingAs($user)->get("/books/{$book->id}")
            ->assertStatus(200)
            ->assertSee($book->title);
    }

    public function test_user_can_update_their_own_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create(['title' => '旧タイトル']);
        $genre = Genre::firstOrCreate(['name' => 'テスト用ジャンル']);

        $updateData = [
            'title' => '新タイトル',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->put("/books/{$book->id}", $updateData);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '新タイトル',
        ]);
    }

    public function test_user_can_delete_their_own_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/books/{$book->id}");

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_user_cannot_update_or_delete_others_book_via_policy(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $responseEdit = $this->actingAs($otherUser)->get("/books/{$book->id}/edit");
        $this->assertTrue(in_array($responseEdit->status(), [403, 302, 401]));

        $responseUpdate = $this->actingAs($otherUser)->put("/books/{$book->id}", [
            'title' => '改ざんタイトル',
            'author' => '改ざん著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
        ]);
        $this->assertTrue(in_array($responseUpdate->status(), [403, 302, 401]));

        $responseDelete = $this->actingAs($otherUser)->delete("/books/{$book->id}");
        $this->assertTrue(in_array($responseDelete->status(), [403, 302, 401, 200]));

        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }
}
