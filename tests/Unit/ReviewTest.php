<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function review_belongs_to_book()
    {
        $book = \App\Models\Book::factory()->create();

        $review = \App\Models\Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertEquals($book->id, $review->book->id);
    }
}
