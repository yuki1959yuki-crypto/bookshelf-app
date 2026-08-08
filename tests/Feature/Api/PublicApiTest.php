<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_api_and_get_json(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);
    }

    public function test_returns_404_json_when_resource_does_not_exist(): void
    {
        $response = $this->getJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }
}
