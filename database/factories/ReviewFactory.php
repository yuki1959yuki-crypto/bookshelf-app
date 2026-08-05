<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'rating' => 5,
            'comment' => 'とても面白かったです',
        ];
    }
}
