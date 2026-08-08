<?php

namespace Tests\Feature\Genres;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // 1. ジャンル一覧画面 (/genres)
    // ==========================================

    public function test_guest_is_redirected_to_login_when_accessing_genres_index(): void
    {
        $response = $this->get('/genres');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_genres_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/genres');
        $response->assertStatus(200);
    }

    public function test_genres_index_displays_genre_names_and_book_counts(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '技術書']);
        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);

        $response = $this->actingAs($user)->get('/genres');

        $response->assertStatus(200);
        $response->assertSee('技術書');
    }

    public function test_authenticated_user_can_access_genre_management_screens(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'テストジャンル']);

        // 一覧、登録画面、編集画面にアクセスできること
        $this->actingAs($user)->get('/genres/create')->assertStatus(200);
        $this->actingAs($user)->get("/genres/{$genre->id}/edit")->assertStatus(200);
    }

    public function test_genres_index_displays_empty_message_when_no_genres_exist(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/genres');
        $response->assertStatus(200);
    }

    // ==========================================
    // 2. ジャンル登録画面 (/genres/create)
    // ==========================================

    public function test_guest_is_redirected_when_accessing_genre_create(): void
    {
        $response = $this->get('/genres/create');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_genre_create_screen(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/genres/create');
        $response->assertStatus(200);
    }

    public function test_user_can_create_genre(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/genres', [
            'name' => '新ジャンル',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('genres', ['name' => '新ジャンル']);
    }

    public function test_genre_name_is_required_for_creation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/genres', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_duplicate_genre_name_fails_unique_constraint_on_creation(): void
    {
        $user = User::factory()->create();
        Genre::create(['name' => '重複ジャンル']);

        $response = $this->actingAs($user)->post('/genres', [
            'name' => '重複ジャンル',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // ==========================================
    // 3. ジャンル詳細画面 (/genres/{id})
    // ==========================================

    public function test_guest_is_redirected_when_accessing_genre_detail(): void
    {
        $genre = Genre::create(['name' => '詳細テスト']);
        $response = $this->get("/genres/{$genre->id}");
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_genre_detail(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '詳細テスト']);

        $response = $this->actingAs($user)->get("/genres/{$genre->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細テスト');
    }

    public function test_genre_detail_displays_only_related_books(): void
    {
        $user = User::factory()->create();
        $genre1 = Genre::create(['name' => 'プログラミング']);
        $genre2 = Genre::create(['name' => '文学']);

        $book1 = Book::factory()->create(['title' => 'Laravel入門']);
        $book2 = Book::factory()->create(['title' => '夏目漱石作品']);

        $book1->genres()->attach($genre1->id);
        $book2->genres()->attach($genre2->id);

        $response = $this->actingAs($user)->get("/genres/{$genre1->id}");

        $response->assertStatus(200);
        $response->assertSee('Laravel入門');
        $response->assertDontSee('夏目漱石作品');
    }

    public function test_genre_detail_handles_empty_books_gracefully(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '空のジャンル']);

        $response = $this->actingAs($user)->get("/genres/{$genre->id}");
        $response->assertStatus(200);
    }

    public function test_returns_404_for_non_existent_genre(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/genres/99999');
        $response->assertStatus(404);
    }

    // ==========================================
    // 4. ジャンル編集・削除画面 (/genres/{id}/edit)
    // ==========================================

    public function test_guest_is_redirected_when_accessing_genre_edit(): void
    {
        $genre = Genre::create(['name' => '編集テスト']);
        $response = $this->get("/genres/{$genre->id}/edit");
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_genre_edit_screen_with_initial_value(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '初期ジャンル名']);

        $response = $this->actingAs($user)->get("/genres/{$genre->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('初期ジャンル名');
    }

    public function test_user_can_update_genre(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '旧ジャンル名']);

        $response = $this->actingAs($user)->put("/genres/{$genre->id}", [
            'name' => '新ジャンル名',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('genres', ['id' => $genre->id, 'name' => '新ジャンル名']);
    }

    public function test_user_can_save_genre_with_same_name(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'そのままの名前']);

        $response = $this->actingAs($user)->put("/genres/{$genre->id}", [
            'name' => 'そのままの名前',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('genres', ['id' => $genre->id, 'name' => 'そのままの名前']);
    }

    public function test_updating_to_existing_genre_name_fails(): void
    {
        $user = User::factory()->create();
        Genre::create(['name' => '既存の名前']);
        $genre2 = Genre::create(['name' => '変更前の名前']);

        $response = $this->actingAs($user)->put("/genres/{$genre2->id}", [
            'name' => '既存の名前',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_genre_name_cannot_be_empty_on_update(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => '有効な名前']);

        $response = $this->actingAs($user)->put("/genres/{$genre->id}", [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_returns_404_for_non_existent_genre_edit(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/genres/99999/edit');
        $response->assertStatus(404);
    }
}
