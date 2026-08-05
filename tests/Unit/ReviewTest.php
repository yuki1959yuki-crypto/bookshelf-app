<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function review_belongs_to_book()
    {
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertEquals($book->id, $review->book->id);
    }
}
