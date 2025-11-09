<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'popularity');

        $now = Carbon::now();
        $startThisMonth = $now->copy()->subDays(30);
        $startLastMonth = $now->copy()->subDays(60);

        $authors = Author::query()
            ->select('authors.*')
            // Total ratings (popularity)
            ->withCount(['books as total_ratings' => fn($q) => $q->join('ratings', 'books.id', '=', 'ratings.book_id')])
            // Avg rating
            ->withAvg('books as avg_book_rating', 'avg_rating')
            ->take(100) // batasi untuk performa
            ->get();

        if ($tab === 'trending') {
            // Ambil trending score langsung dari DB
            $authors->map(function ($author) use ($startThisMonth, $startLastMonth) {
                $bookIds = $author->books()->pluck('id');

                $avgThisMonth = Rating::whereIn('book_id', $bookIds)
                    ->where('created_at', '>=', $startThisMonth)
                    ->avg('score') ?? 0;

                $avgLastMonth = Rating::whereIn('book_id', $bookIds)
                    ->whereBetween('created_at', [$startLastMonth, $startThisMonth])
                    ->avg('score') ?? 0;

                $votesCount = Rating::whereIn('book_id', $bookIds)
                    ->where('created_at', '>=', $startThisMonth)
                    ->count();

                $author->trending_score = ($avgThisMonth - $avgLastMonth) * $votesCount;
                return $author;
            });

            $authors = $authors->sortByDesc('trending_score')->take(20);
        } else if ($tab === 'popularity') {
            $authors = $authors->sortByDesc('total_ratings')->take(20);
        } else if ($tab === 'rating') {
            $authors = $authors->sortByDesc('avg_book_rating')->take(20);
        }

        // Best / Worst book bisa dihitung HANYA untuk top 20 authors
        $authors->each(function ($author) {
            $author->best_book = $author->books()->orderByDesc('avg_rating')->first();
            $author->worst_book = $author->books()->orderBy('avg_rating')->first();
        });

        return view('authors.index', compact('authors', 'tab'));
    }
}
