<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AuthorsSeeder extends Seeder {
    public function run(): void {
        echo "Membuat data Authors (1000 records)...\n";
        $start = microtime(true);

        try {
            $total = 1000;
            $chunk = 500;

            for ($i = 0; $i < ceil($total / $chunk); $i++) {
                $records = Author::factory()->count($chunk)->make()->map(function ($item) {
                    $arr = $item->getAttributes();
                    $arr['created_at'] = now()->format('Y-m-d H:i:s');
                    $arr['updated_at'] = now()->format('Y-m-d H:i:s');
                    return $arr;
                })->toArray();

                DB::table('authors')->insert($records);
                echo "Batch " . ($i + 1) . " selesai.\n";
            }

            Cache::flush();
            echo "Cache dibersihkan.\n";
            echo "AuthorsSeeder selesai dalam " . round(microtime(true) - $start, 2) . " detik.\n\n";
        } catch (\Throwable $e) {
            echo "Gagal AuthorsSeeder: {$e->getMessage()}\n";
        }
    }
}
