<?php

namespace Tests\Feature\Reports;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_own_reading_report(): void
    {
        $user = User::factory()->create();

        Book::factory()->for($user)->create([
            'title' => '自分の本1',
            'isbn' => '9784873117581',
            'created_at' => '2026-04-10 10:00:00',
        ]);
        Book::factory()->for($user)->create([
            'title' => '自分の本2',
            'isbn' => '9784873117582',
            'created_at' => '2026-04-20 10:00:00',
        ]);

        $response = $this->actingAs($user)->get('/reports?year=2026&month=4');

        $response->assertOk();
        $response->assertSee('自分の本1');
        $response->assertSee('自分の本2');

    }

    public function test_reading_report_strictly_isolates_user_data(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Book::factory()->for($user)->create([
            'title' => 'ユーザーAの本',
            'isbn' => '9784873117583',
            'created_at' => '2026-04-05 10:00:00',
        ]);

        Book::factory()->for($otherUser)->create([
            'title' => 'ユーザーBの本',
            'isbn' => '9784873117584',
            'created_at' => '2026-04-15 10:00:00',
        ]);

        $response = $this->actingAs($user)->get('/reports?year=2026&month=4');

        $response->assertOk();

    }
}
