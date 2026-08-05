<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
   /** @test */
   public function user_has_many_books()
    {
    
        $user = \App\Models\User::factory()->create();

        \App\Models\Book::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        
        $this->assertCount(3, $user->books);
    }
}
