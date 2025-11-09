<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Book;

class RatingFactory extends Factory {
    public function definition(): array {
        $now = now()->format('Y-m-d H:i:s');
        return [
            'user_id' => rand(1, 5000),
            'book_id' => rand(1, 100000),
            'score' => rand(1, 10),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
