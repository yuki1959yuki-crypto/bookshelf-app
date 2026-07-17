<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::first();

        if (! $creator) {
            return;
        }

        $booksData = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '吾輩は猫であるは、夏目漱石による日本の小説で、1905年に発表されました。この作品は、猫の視点から人間社会を風刺的に描いており、ユーモアと哲学的な要素が特徴です。',
                'genres' => ['小説'],
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'description' => '人を動かすは、D・カーネギーによるビジネス書で、人間関係とリーダーシップについて述べた作品です。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'description' => 'リーダブルコードは、Dustin Boswellによるプログラミングに関する技術書で、コードの可読性を向上させるためのベストプラクティスを紹介しています。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246 ',
                'published_date' => '2013-08-30',
                'description' => '7つの習慣は、スティーブン・R・コヴィーによる自己啓発書で、人生の目標設定と行動計画について述べた作品です。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021 ',
                'published_date' => '1906-04-01',
                'description' => '坊っちゃんは、夏目漱石による日本の小説で、1906年に発表されました。この作品は、子供の視点から人間社会を描いており、ユーモアと感動の要素が特徴です。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712 ',
                'published_date' => '2016-09-08',
                'description' => 'サピエンス全史は、ユヴァル・ノア・ハラリによる歴史書で、人類の歴史を俯瞰的に解説しています。',
                'genres' => ['歴史', '科学'],
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'description' => 'Clean Codeは、Robert C. Martinによるプログラミングに関する技術書で、コードの品質を向上させるためのベストプラクティスを紹介しています。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'description' => '嫌われる勇気は、岸見一郎・古賀史健による心理学書で、人間関係と自己理解について述べた作品です。',
                'genres' => ['自己啓発'],
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'description' => '火花は、又吉直樹による小説で、戦後の日本社会を背景にした物語です。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'description' => 'FACTFULNESSは、ハンス・ロスリングによる統計学書で、世界の現状についての理解を深めるための知識を提供しています。',
                'genres' => ['ビジネス', '科学'],
            ],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'description' => 'コンテナ物語は、マルク・レビンソンによる旅行記で、世界中のコンテナ端末を巡る物語です。',
                'genres' => ['ビジネス', '歴史'],
            ],
        ];
        foreach ($booksData as $index => $book) {
            $num = $index + 1;

            $createdBook = Book::firstOrCreate(
                ['isbn' => $book['isbn']],
                [
                    'user_id' => $creator->id,
                    'title' => $book['title'],
                    'author' => $book['author'],
                    'published_date' => $book['published_date'],
                    'description' => $book['description'],
                    'image_url' => "https://placehold.co/200x300/e2e8f0/475569?text={$num}",
                ]
            );
            $genreIds = DB::table('genres')
                ->whereIn('name', $book['genres'])
                ->pluck('id')
                ->toArray();
            $createdBook->genres()->sync($genreIds);

        }
    }
}
