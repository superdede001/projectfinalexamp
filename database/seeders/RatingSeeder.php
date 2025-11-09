<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RatingsSeeder extends Seeder {
    public function run(): void {
        echo "Membuat data Ratings (500.000 records)...\n";
        $start = microtime(true);

        try {
            $total = 500000;
            $chunk = 10000;

            for ($i = 0; $i < ceil($total / $chunk); $i++) {
                $records = Rating::factory()->count($chunk)->make()->map(function ($item) {
                    $arr = $item->getAttributes();
                    $arr['user_id'] = rand(1, 5000);
                    $arr['book_id'] = rand(1, 100000);
                    $arr['score'] = rand(1, 10);

                    $daysAgo = rand(0, 60); 
                    $createdAt = Carbon::now()->subDays($daysAgo)->format('Y-m-d H:i:s');

                    $arr['created_at'] = $createdAt;
                    $arr['updated_at'] = $createdAt;

                    return $arr;
                })->toArray();

                DB::table('ratings')->insert($records);
                echo "Batch " . ($i + 1) . " selesai.\n";
            }

            Cache::flush();
            echo "Cache dibersihkan.\n";
            echo "RatingsSeeder selesai dalam " . round(microtime(true) - $start, 2) . " detik.\n\n";
        } catch (\Throwable $e) {
            echo "Gagal RatingsSeeder: {$e->getMessage()}\n";
        }
    }
}
