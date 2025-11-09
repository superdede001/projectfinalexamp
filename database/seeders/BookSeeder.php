<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        echo "Membuat data Books (100.000 records)...\n";
        $start = microtime(true);

        try {
            $faker = \Faker\Factory::create();

            $total = 100000;
            $chunk = 1000;

            $authorIds = array_filter(array_map('intval', DB::table('authors')->pluck('id')->all()));
            $categoryIds = array_filter(array_map('intval', DB::table('categories')->pluck('id')->all()));
            $statuses = ['available', 'rented', 'reserved'];

            if (empty($authorIds) || empty($categoryIds)) {
                throw new \Exception("Tabel authors atau categories masih kosong. Jalankan seedernya dulu.");
            }

            for ($i = 0; $i < ceil($total / $chunk); $i++) {
                $count = min($chunk, $total - $i * $chunk);

                $records = collect(range(1, $count))->map(function ($index) use ($faker, $authorIds, $categoryIds, $statuses, $i, $chunk) {
                    $isbn = sprintf('978%09d', $i * $chunk + $index);

                    return [
                        'title' => $faker->sentence(3),
                        'isbn' => $isbn,
                        'author_id' => $faker->randomElement($authorIds),
                        'category_id' => $faker->randomElement($categoryIds),
                        'publisher' => $faker->company(),
                        'publication_year' => $faker->numberBetween(1980, 2025),
                        'availability_status' => $faker->randomElement($statuses),
                        'store_location' => $faker->city() . ' Branch',
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                DB::table('books')->insert($records);

                echo "Batch " . ($i + 1) . " dari " . ceil($total / $chunk) . " selesai.\n";
            }

            Cache::flush();
            $time = round(microtime(true) - $start, 2);

            echo "Cache berhasil dihapus.\n";
            echo "Selesai membuat data Books. ({$time}s)\n\n";

        } catch (\Throwable $e) {
            echo "Gagal BooksSeeder: {$e->getMessage()}\n";
        }
    }
}
