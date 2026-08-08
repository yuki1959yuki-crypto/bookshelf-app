<?php

namespace Tests\Feature\Security;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XssTest extends TestCase
{
    use RefreshDatabase;

    public function test_input_containing_script_tags_is_escaped_on_display()
    {
        $user = User::factory()->create();

        $maliciousTitle = '<script>alert("XSS")</script>テスト書籍';

        $book = Book::factory()->for($user)->create([
            'title' => $maliciousTitle,
            'author' => 'テスト著者',
        ]);

        $detailResponse = $this->actingAs($user)->get("/books/{$book->id}");

        $detailResponse->assertDontSee($maliciousTitle, false);
        $detailResponse->assertSee('&lt;script&gt;', false);
    }
}
