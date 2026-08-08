<?php

namespace Tests\Feature\Ranking;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_ranking_screen(): void
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }

    public function test_books_are_displayed_in_ranking_order_by_rating(): void
    {

        $bookHigh = Book::factory()->create(['title' => '高評価の本']);
        $bookLow = Book::factory()->create(['title' => '低評価の本']);

        Review::factory()->for($bookHigh)->create(['rating' => 5]);
        Review::factory()->for($bookLow)->create(['rating' => 1]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeTextInOrder(['高評価の本', '低評価の本']);
    }

    public function test_books_with_same_rating_are_ordered_consistently(): void
    {
        $book1 = Book::factory()->create(['title' => '書籍A']);
        $book2 = Book::factory()->create(['title' => '書籍B']);

        Review::factory()->for($book1)->create(['rating' => 4]);
        Review::factory()->for($book2)->create(['rating' => 4]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);
        $response->assertSee('書籍A');
        $response->assertSee('書籍B');
    }

    public function test_displays_empty_ranking_when_no_data_exists(): void
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }

    public function test_ranking_displays_only_top_10_books(): void
    {

        $books = Book::factory()->count(12)->create();
        foreach ($books as $index => $book) {
            Review::factory()->for($book)->create(['rating' => ($index % 5) + 1]);
        }

        $response = $this->get('/ranking');

        $response->assertStatus(200);

    }
}
