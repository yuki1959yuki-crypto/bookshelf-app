<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function book_belongs_to_user()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $book->user->id);
    }
}
