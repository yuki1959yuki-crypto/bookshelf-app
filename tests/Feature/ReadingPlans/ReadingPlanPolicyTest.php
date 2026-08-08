<?php

namespace Tests\Feature\ReadingPlans;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_their_own_reading_plan(): void
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $readingPlan = ReadingPlan::factory()->for($owner)->for($book)->create([
            'target_date' => '2026-12-31',
            'target_pages' => 200,
            'status' => ReadingPlanStatus::NotStarted,
        ]);

        $response = $this->actingAs($owner)->put("/reading-plans/{$readingPlan->id}", [
            'target_date' => '2027-01-31',
            'target_pages' => 250,
            'status' => ReadingPlanStatus::Reading->value,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'target_date' => '2027-01-31',
        ]);
    }

    public function test_other_user_cannot_update_others_reading_plan(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $readingPlan = ReadingPlan::factory()->for($owner)->for($book)->create([
            'target_date' => '2026-12-31',
            'target_pages' => 200,
            'status' => ReadingPlanStatus::NotStarted,
        ]);

        $response = $this->actingAs($otherUser)->put("/reading-plans/{$readingPlan->id}", [
            'target_date' => '2027-01-31',
            'target_pages' => 250,
            'status' => ReadingPlanStatus::Reading->value,
        ]);

        $response->assertForbidden();
    }
}
