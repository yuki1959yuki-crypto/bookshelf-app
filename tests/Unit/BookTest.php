<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function book_belongs_to_user()
    {
        $user = \App\Models\User::factory()->create();

        $book = \App\Models\Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $book->user->id);
    }
}
