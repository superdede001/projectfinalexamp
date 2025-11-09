<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory {
    public function definition(): array {
        $now = now()->format('Y-m-d H:i:s');
        return [
            'name' => fake()->name(),
            'email' => fake()->unique(true)->safeEmail(),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
