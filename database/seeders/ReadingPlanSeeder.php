<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $yamada = User::where('name', '山田太郎')->first() ?? User::factory()->create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
        ]);

        $otherUser = User::where('id', '!=', $yamada->id)->first() ?? User::factory()->create([
            'name' => '佐藤花子',
            'email' => 'sato@example.com',
        ]);

        $books = Book::all();
        if ($books->count() < 5) {
            $this->command->warn('BookSeederを先に実行し、十分な書籍データを投入してください。');

            return;
        }

        $today = Carbon::today();

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[0]->id,
            'target_pages' => 300,
            'target_date' => $today->copy()->addDays(10),
            'status' => '読書中',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[1]->id,
            'target_pages' => 200,
            'target_date' => $today->copy()->subDays(3),
            'status' => '読書中',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[2]->id,
            'target_pages' => 150,
            'target_date' => $today->copy()->addDay(),
            'status' => '読書中',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[3]->id,
            'target_pages' => 250,
            'target_date' => $today->copy()->subDays(1),
            'status' => '読了',
        ]);

        ReadingPlan::create([
            'user_id' => $otherUser->id,
            'book_id' => $books[4]->id,
            'target_pages' => 180,
            'target_date' => $today->copy()->addDays(5),
            'status' => '読書中',
        ]);
    }
}
