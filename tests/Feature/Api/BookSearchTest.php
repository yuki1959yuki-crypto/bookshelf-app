<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Book::truncate();
        Genre::truncate();
        Review::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function test_can_search_books_by_keyword(): void
    {
        Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        Book::factory()->create(['title' => 'Vue.jsの教科書', 'author' => '鈴木花子']);

        $response = $this->getJson('/api/v1/books?keyword=Laravel');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Laravel入門']);
    }

    public function test_can_filter_books_by_genre(): void
    {
        $genreTech = Genre::create(['name' => '技術書']);
        $genreNovel = Genre::create(['name' => '小説']);

        $bookTech = Book::factory()->create(['title' => 'Laravel本']);
        $bookNovel = Book::factory()->create(['title' => '夏目漱石作品']);

        $bookTech->genres()->attach($genreTech->id);
        $bookNovel->genres()->attach($genreNovel->id);

        $response = $this->getJson('/api/v1/books?genre_id='.$genreTech->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Laravel本']);
    }

    public function test_can_sort_books_by_rating(): void
    {
        $bookLow = Book::factory()->create(['title' => '低評価の本']);
        $bookHigh = Book::factory()->create(['title' => '高評価の本']);

        Review::factory()->for($bookLow)->create(['rating' => 1]);
        Review::factory()->for($bookHigh)->create(['rating' => 5]);

        $response = $this->getJson('/api/v1/books?sort=rating');

        $response->assertOk();
        $response->assertJsonPath('data.0.title', '高評価の本');
    }

    public function test_can_sort_books_by_title(): void
    {
        Book::factory()->create(['title' => 'A_書籍']);
        Book::factory()->create(['title' => 'B_書籍']);

        $response = $this->getJson('/api/v1/books?sort=title&direction=asc');

        $response->assertOk();
        $response->assertJsonPath('data.0.title', 'A_書籍');
    }
}
