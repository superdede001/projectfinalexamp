<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CategoriesSeeder extends Seeder {
    public function run(): void {
        echo "Membuat data Categories (3000 records)...\n";
        $start = microtime(true);

        try {
            $total = 3000;
            $chunk = 1000;
            fake()->unique(true); // reset unique cache

            for ($i = 0; $i < ceil($total / $chunk); $i++) {
                $records = Category::factory()->count($chunk)->make()->map(function ($item) {
                    $arr = $item->getAttributes();
                    $arr['name'] = ucfirst(fake()->word()) . ' ' . rand(1, 99999); // pastikan unik
                    $arr['created_at'] = now()->format('Y-m-d H:i:s');
                    $arr['updated_at'] = now()->format('Y-m-d H:i:s');
                    return $arr;
                })->toArray();

                DB::table('categories')->insert($records);
                echo "Batch " . ($i + 1) . " selesai.\n";
            }

            Cache::flush();
            echo "Cache dibersihkan.\n";
            echo "CategoriesSeeder selesai dalam " . round(microtime(true) - $start, 2) . " detik.\n\n";
        } catch (\Throwable $e) {
            echo "Gagal CategoriesSeeder: {$e->getMessage()}\n";
        }
    }
}
