<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::all();

        foreach ($reviews as $review) {
            $eligibleUsers = $users->reject(function ($user) use ($review) {
                return $user->id === $review->user_id;
            });

            if ($eligibleUsers->isNotEmpty()) {
                $likeCount = fake()->numberBetween(0, min(3, $eligibleUsers->count()));
                $likers = $eligibleUsers->random($likeCount);
                $review->likers()->syncWithoutDetaching($likers->pluck('id')->toArray());
            }
        }
    }
}
