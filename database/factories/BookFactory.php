<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;
use App\Models\Category;

class BookFactory extends Factory
{
    public function definition(): array
    {
        $now = now()->format('Y-m-d H:i:s');

        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'author_id' => rand(1, 1000),
            'category_id' => rand(1, 3000),
            'publisher' => $this->faker->company(),
            'publication_year' => fake()->numberBetween(1980, 2025),
            'availability_status' => $this->faker->randomElement(['available', 'rented', 'reserved']),
            'store_location' => $this->faker->city(),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
