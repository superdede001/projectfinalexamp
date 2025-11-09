<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory {
    public function definition(): array {
        $now = now()->format('Y-m-d H:i:s');
        return [
            'name' => fake()->name(),
            'bio' => fake()->paragraph(),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
