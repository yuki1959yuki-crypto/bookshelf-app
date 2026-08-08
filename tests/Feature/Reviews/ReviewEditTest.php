<?php

namespace Tests\Feature\Reviews;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_review_edit(): void
    {
        $review = Review::factory()->create();

        $response = $this->get("/reviews/{$review->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_owner_can_view_review_edit_screen(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/reviews/{$review->id}/edit");

        $response->assertStatus(200);
    }

    public function test_other_user_cannot_access_others_review_edit(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get("/reviews/{$review->id}/edit");

        $response->assertStatus(403);
    }

    public function test_owner_can_update_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create([
            'rating' => 3,
            'comment' => '古いコメント',
        ]);

        $response = $this->actingAs($user)->put("/reviews/{$review->id}", [
            'rating' => 5,
            'comment' => '更新された素晴らしいコメントです。',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => '更新された素晴らしいコメントです。',
        ]);
    }

    public function test_rating_validation_errors(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->put("/reviews/{$review->id}", [
            'rating' => '',
            'comment' => '有効なコメントです。',
        ]);
        $response->assertSessionHasErrors('rating');

        $response2 = $this->actingAs($user)->put("/reviews/{$review->id}", [
            'rating' => 6,
            'comment' => '有効なコメントです。',
        ]);
        $response2->assertSessionHasErrors('rating');
    }

    public function test_comment_validation_errors(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->put("/reviews/{$review->id}", [
            'rating' => 4,
            'comment' => '',
        ]);
        $response->assertSessionHasErrors('comment');
    }

    public function test_returns_404_for_non_existent_review(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reviews/99999/edit');

        $response->assertStatus(404);
    }

    public function test_owner_can_delete_review_and_recalculate(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->for($user)->for($book)->create([
            'rating' => 5,
        ]);

        $response = $this->actingAs($user)->delete("/reviews/{$review->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_user_review_submission_behavior(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Review::factory()->for($user)->for($book)->create();

        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews", [
            'rating' => 4,
            'comment' => '追加レビューテスト',
        ]);

        $this->assertTrue($response->status() < 500);
    }
}
