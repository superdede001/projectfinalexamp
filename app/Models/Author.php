<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];
    public $trending_score; // Properti untuk menyimpan skor trending sementara

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * STATISTIK: Hitung total ratings received oleh semua buku penulis ini.
     */
    public function getTotalRatingsReceived()
    {
        // Pilihan: Menggunakan denormalisasi votes_count dari tabel books
        return $this->books->sum('votes_count');

        // Atau menggunakan query langsung ke tabel ratings (lebih akurat jika data tidak sinkron):
        /*
        return DB::table('ratings')
            ->join('books', 'ratings.book_id', '=', 'books.id')
            ->where('books.author_id', $this->id)
            ->count('ratings.id');
        */
    }

    /**
     * STATISTIK: Mengambil buku terbaik dan terburuk penulis berdasarkan avg_rating.
     */
    public function getBestWorstBook()
    {
        // Ambil ID buku penulis
        $bookIds = $this->books->pluck('id');

        // Cari buku dengan votes_count minimal 5 untuk hasil yang relevan
        $baseQuery = Book::whereIn('id', $bookIds)
            ->where('votes_count', '>', 5)
            ->select('judul', 'avg_rating', 'votes_count');

        $bestBook = (clone $baseQuery)->orderByDesc('avg_rating')->orderByDesc('votes_count')->first();
        $worstBook = (clone $baseQuery)->orderBy('avg_rating')->orderBy('votes_count')->first();

        return [
            'best' => $bestBook,
            'worst' => $worstBook,
        ];
    }

    /**
     * LOGIC CHALLENGE: Hitung skor trending penulis.
     * Skor = (Diff Avg Rating Recent vs Previous 30 days) * log(Weight Voter Count Recent)
     */
    public function calculateTrendingScore(): float
    {
        $recentStart = Carbon::now()->subDays(30);
        $previousStart = Carbon::now()->subDays(60);

        // Ambil ID buku penulis
        $bookIds = $this->books->pluck('id');

        // Query Statistik 60 Hari Terakhir
        $stats = DB::table('ratings')
            ->select(
                DB::raw('AVG(CASE WHEN created_at >= ? THEN score END) as recent_avg'),
                DB::raw('COUNT(CASE WHEN created_at >= ? THEN id END) as recent_count'),
                DB::raw('AVG(CASE WHEN created_at BETWEEN ? AND ? THEN score END) as prev_avg')
            )
            ->whereIn('book_id', $bookIds)
            ->setBindings([$recentStart, $recentStart, $previousStart, $recentStart])
            ->first();

        $recentAvg = $stats->recent_avg ?? 0;
        $previousAvg = $stats->prev_avg ?? 0;
        $recentCount = $stats->recent_count ?? 0;

        // Formula Trending Score
        $avgDifference = $recentAvg - $previousAvg;
        $voterWeight = log(max(1, $recentCount)); // Menggunakan log untuk menormalisasi

        $trendingScore = $avgDifference * $voterWeight;

        // Simpan skor ini di properti Model untuk diakses di Controller/View
        $this->trending_score = $trendingScore;

        return $trendingScore;
    }
}
