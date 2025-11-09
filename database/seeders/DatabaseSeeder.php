<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            UsersSeeder::class,
            AuthorsSeeder::class,
            CategoriesSeeder::class,
            BooksSeeder::class,
            RatingsSeeder::class,
        ]);
    }
}
