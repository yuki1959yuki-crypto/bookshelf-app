<?php

namespace Tests\Feature\ReadingPlans;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)->postJson('/reading-plans', [
            'book_id' => $book->id,
            'target_date' => '2026-12-31',
            'target_pages' => 300,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => '2026-12-31',
        ]);
    }

    public function test_cannot_create_duplicate_active_reading_plan_for_same_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        ReadingPlan::factory()->for($user)->for($book)->create([
            'status' => ReadingPlanStatus::Reading,
            'target_pages' => 100,
            'target_date' => '2026-10-31',
        ]);

        $response = $this->actingAs($user)->post('/reading-plans', [
            'book_id' => $book->id,
            'target_date' => '2027-01-01',
            'target_pages' => 200,
        ]);

        $response->assertSessionHasErrors('book_id');
    }
}
