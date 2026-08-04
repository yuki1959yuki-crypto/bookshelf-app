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
        $comments = [
            1 => ['期待していた内容と異なり、少し残念でした。', 'あまり自分には合いませんでした。', '内容が難しく、途中で読むのが止まってしまいました。'],
            2 => ['もう少し詳しい説明があると良かったです。', '可もなく不可もなくという印象です。', '期待していたほどではありませんでした。'],
            3 => ['普通に読める一冊でした。', '期待通りの内容でした。', '全体的に標準的な内容でした。'],
            4 => ['とても参考になりました。おすすめできます。', '面白かったです。買って良かったと思います。', '期待していたよりも良かったです。'],
            5 => ['素晴らしい名著でした。何度も読み返したくなります。', '感動しました！最高の一冊です。', '非常にためになる内容でした。'],
        ];

        foreach ($books as $book) {
            $numReviewsToCreate = rand(2, 4);

            $selectedUsers = $users->random(min($numReviewsToCreate, $users->count()));

            foreach ($selectedUsers as $user) {
                $rating = rand(1, 5);
                $comment = $comments[$rating][array_rand($comments[$rating])];

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            }
        }
    }
}
