<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory {
    public function definition(): array {
        $now = now()->format('Y-m-d H:i:s');
        return [
            'name' => ucfirst(fake()->word()) . ' ' . fake()->unique()->numberBetween(1, 1000000),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
