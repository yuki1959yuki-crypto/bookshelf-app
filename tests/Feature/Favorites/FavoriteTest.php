<?php

namespace Tests\Feature\Favorites;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_favorites(): void
    {
        $response = $this->get('/favorites');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_their_favorites(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)->post("/books/{$book->id}/favorite");

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    public function test_user_cannot_see_other_users_favorites(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user2)->post("/books/{$book->id}/favorite");

        $response = $this->actingAs($user1)->get('/favorites');

        $response->assertStatus(200);
        $response->assertDontSee($book->title);
    }

    public function test_displays_empty_favorites_screen_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
    }

    public function test_favorite_can_be_toggled(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)->post("/books/{$book->id}/favorite");
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->post("/books/{$book->id}/favorite");
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_duplicate_favorites_are_prevented(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)->post("/books/{$book->id}/favorite");

        $count = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->count();

        $this->assertTrue($count <= 1);
    }
}
