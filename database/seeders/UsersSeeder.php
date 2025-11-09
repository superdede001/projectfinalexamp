<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder {
    public function run(): void {
        echo "Membuat data Users (5000 records unik)...\n";
        $start = microtime(true);

        try {
            $total = 5000;
            $chunk = 1000;

            for ($i = 0; $i < ceil($total / $chunk); $i++) {
                $records = [];

                for ($j = 0; $j < $chunk; $j++) {
                    $index = $i * $chunk + $j + 1;

                    $records[] = [
                        'name' => fake()->name(),
                        'email' => "user{$index}_" . Str::random(5) . "@example.com", // unik dijamin
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                }

                DB::table('users')->insert($records);
                echo "Batch " . ($i + 1) . " selesai.\n";
            }

            Cache::flush();
            echo "Cache dibersihkan.\n";
            echo "UsersSeeder selesai dalam " . round(microtime(true) - $start, 2) . " detik.\n\n";
        } catch (\Throwable $e) {
            echo "Gagal UsersSeeder: {$e->getMessage()}\n";
        }
    }
}
