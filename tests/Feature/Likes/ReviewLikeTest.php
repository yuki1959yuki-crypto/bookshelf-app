<?php

namespace Tests\Feature\Likes;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_trying_to_like(): void
    {
        $review = Review::factory()->create();

        $response = $this->post("/reviews/{$review->id}/like");

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_like_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->post("/reviews/{$review->id}/like");

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_like_can_be_toggled_off_on_second_press(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($user)->post("/reviews/{$review->id}/like");

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)->post("/reviews/{$review->id}/like");

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_duplicate_likes_are_prevented(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        DB::table('review_likes')->insert([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)->post("/reviews/{$review->id}/like");

        $response->assertSessionHasNoErrors();

        $count = DB::table('review_likes')
            ->where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->count();

        $this->assertTrue($count <= 1);
    }
}
