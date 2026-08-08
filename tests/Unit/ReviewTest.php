<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
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

    /** @test */
    public function review_belongs_to_user()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $review->user->id);
    }

    /** @test */
    public function review_has_many_review_likes()
    {
        $review = Review::factory()->create();

        ReviewLike::factory()->count(2)->create([
            'review_id' => $review->id,
        ]);

        $this->assertCount(2, $review->likes);
    }
}
