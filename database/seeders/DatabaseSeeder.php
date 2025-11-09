<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Category;
use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $faker = Faker::create();
        $booksToCreate = 100000;
        $ratingsToCreate = 500000;
        $batchSize = 5000;

        // 1) Authors (1,000)
        $this->command->info('Seeding 1,000 authors...');
        Author::factory(1000)->create();
        $authorIds = Author::pluck('id')->toArray();

        // 2) Categories (3,000)
        $this->command->info('Seeding 3,000 categories...');
        Category::factory(3000)->create();
        $categoryIds = Category::pluck('id')->toArray();

        // 3) Users (50,000)
        $this->command->info('Seeding 50,000 users...');
        User::factory(50000)->create();
        $userIds = User::pluck('id')->toArray();

        // 4) Books (100,000) & Attaching Categories
        $this->command->info('Seeding 100,000 books...');
        for ($i = 0; $i < ceil($booksToCreate / $batchSize); $i++) {
            $books = Book::factory(min($batchSize, $booksToCreate - ($i * $batchSize)))->create([
                'author_id' => fn() => $faker->randomElement($authorIds),
                'avg_rating' => 0,
                'votes_count' => 0,
                'recent_popularity_score' => 0,
            ]);
            $syncData = [];
            foreach ($books as $book) {
                $numCats = $faker->numberBetween(1, 3);
                $randomCats = $faker->randomElements($categoryIds, $numCats);
                foreach ($randomCats as $catId) {
                    $syncData[] = ['book_id' => $book->id, 'category_id' => $catId];
                }
            }
            DB::table('book_category')->insert($syncData);
            $this->command->info("Books batch " . ($i + 1) . " created");
        }
        $bookIds = Book::pluck('id')->toArray();

        // 5) Ratings 
        $this->command->info("Seeding {$ratingsToCreate} ratings...");
        $ratingsBatchSize = 2000;

        for ($i = 0; $i < ceil($ratingsToCreate / $ratingsBatchSize); $i++) {
            $rows = [];
            $limit = min($ratingsBatchSize, $ratingsToCreate - ($i * $ratingsBatchSize));

            for ($j = 0; $j < $limit; $j++) {
                $isGuest = $faker->boolean(50);

                $rows[] = [
                    'book_id' => $faker->randomElement($bookIds),
                    'user_id' => $isGuest ? null : $faker->randomElement($userIds),
                    'guest_ip' => $isGuest ? $faker->ipv4() : null,
                    'score' => $faker->numberBetween(1, 10),
                    'comment' => $faker->optional()->sentence(),
                    'created_at' => now()->subDays(rand(0, 90))->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            DB::table('ratings')->insert($rows);
            $this->command->info("Inserted ratings batch " . ($i + 1));
        }

        // 6) Update denormalized stats for books (batch update)
        $this->command->info('Calculating avg_rating and votes_count for books...');
        $priorCount = 10;
        $priorMean = 3.5;

        $this->command->getOutput()->progressStart($booksToCreate);

        Book::select('id')->chunk(2000, function ($books) use ($priorCount, $priorMean, $faker) {
            $chunkIds = $books->pluck('id')->toArray();

            $stats = DB::table('ratings')
                ->select('book_id', DB::raw('AVG(score) as avg'), DB::raw('COUNT(*) as cnt'))
                ->whereIn('book_id', $chunkIds)
                ->groupBy('book_id')
                ->get();

            $statsMap = $stats->keyBy('book_id');

            foreach ($books as $book) {
                $s = $statsMap->get($book->id);

                $avgRating = $s ? round($s->avg, 2) : 7.5;

                $votesCount = $faker->numberBetween(400, 25000);

                $popularityScore = (
                    ($priorMean * $priorCount) + ($avgRating * $votesCount)
                ) / ($priorCount + $votesCount);

                DB::table('books')->where('id', $book->id)->update([
                    'avg_rating' => $avgRating,
                    'votes_count' => $votesCount,
                    'recent_popularity_score' => round($popularityScore, 2),
                    'updated_at' => now(),
                ]);
                $this->command->getOutput()->progressAdvance(1);
            }
        });

        $this->command->getOutput()->progressFinish();
        $this->command->info('Seeding complete.');
    }
}
