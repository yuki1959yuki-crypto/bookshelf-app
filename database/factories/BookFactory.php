<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->realText(20),
            'author' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'published_date' => fake()->date(),
            'description' => fake()->realText(100),
        ];
    }
}
