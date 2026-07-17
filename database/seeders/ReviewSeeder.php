<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }
        $distribution = [
            4,
            4,
            3,
            3,
            3,
            3,
            3,
            3,
            2,
            2,
            2,
        ];
        $comments = [
            5 => ['素晴らしい名著でした。何度も読み返したくなります。', '感動しました！期待以上の内容でした。', '心に響く一冊でした。'],
            4 => ['期待していたよりも良かったです。', 'とても参考になりました。', '面白かったです。'],
            3 => ['内容はよかったですが、少し難しかったです。', '期待通りの内容でした。', 'まあまあでした。'],
        ];

        $reviewCount = 0;
        foreach ($books as $index => $book) {
            $numReviewsToCreate = $distribution[$index] ?? 3;

            $shuffledUsers = $users->shuffle();

            for ($i = 0; $i < $numReviewsToCreate; $i++) {
                $user = $shuffledUsers[$i % $shuffledUsers->count()];
                $rating = fake()->numberBetween(3, 5);
                $comment = $comments[$rating][array_rand($comments[$rating])];

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);

                $reviewCount++;
            }
        }
    }
}
