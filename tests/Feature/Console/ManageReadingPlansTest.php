<?php

namespace Tests\Feature\Console;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManageReadingPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_batch_expires_old_plans_and_sends_notifications()
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $expiredPlan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'target_date' => now()->subDay()->toDateString(),
                'status' => ReadingPlanStatus::NotStarted,
                'target_pages' => 100,
            ]);

        $reminderPlan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'target_date' => now()->addDays(3)->toDateString(),
                'status' => ReadingPlanStatus::NotStarted,
                'target_pages' => 100,
            ]);

        $this->artisan('app:manage-reading-plans')->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $expiredPlan->id,
            'status' => ReadingPlanStatus::Expired,
        ]);

        Notification::assertSentTo($user, ReadingPlanReminder::class);
    }
}
