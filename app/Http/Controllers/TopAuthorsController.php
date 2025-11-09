<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopAuthorsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'popularity'); 

        $authorsQuery = Author::query()
            ->select('authors.id', 'authors.name')
            ->withCount([
                'books as total_books'
            ])
            ->with([
                'books' => function ($q) {
                    $q->withAvg('ratings as avg_rating', 'score');
                    $q->withCount('ratings');
                }
            ]);

        if ($tab === 'popularity') {
            $authors = Rating::select('authors.id', 'authors.name',
                    DB::raw('COUNT(ratings.id) as voters_count'),
                    DB::raw('AVG(ratings.score) as avg_rating')
                )
                ->join('books', 'books.id', '=', 'ratings.book_id')
                ->join('authors', 'authors.id', '=', 'books.author_id')
                ->where('ratings.score', '>', 5)
                ->groupBy('authors.id', 'authors.name')
                ->orderByDesc('voters_count')
                ->limit(20)
                ->get();
        }

        elseif ($tab === 'average') {
            $authors = Rating::select('authors.id', 'authors.name',
                    DB::raw('AVG(ratings.score) as avg_rating'),
                    DB::raw('COUNT(ratings.id) as voters_count')
                )
                ->join('books', 'books.id', '=', 'ratings.book_id')
                ->join('authors', 'authors.id', '=', 'books.author_id')
                ->groupBy('authors.id', 'authors.name')
                ->orderByDesc('avg_rating')
                ->limit(20)
                ->get();
        }

        else {
            $now = now();
            $recent_start = $now->copy()->subDays(30);
            $last_month_start = $now->copy()->subDays(60);
            $last_month_end = $now->copy()->subDays(30);

            $authors = Rating::select(
                    'authors.id',
                    'authors.name',
                    DB::raw('AVG(CASE WHEN ratings.created_at BETWEEN "' . $recent_start . '" AND "' . $now . '" THEN ratings.score END) as recent_avg'),
                    DB::raw('AVG(CASE WHEN ratings.created_at BETWEEN "' . $last_month_start . '" AND "' . $last_month_end . '" THEN ratings.score END) as last_avg'),
                    DB::raw('COUNT(ratings.id) as total_votes')
                )
                ->join('books', 'books.id', '=', 'ratings.book_id')
                ->join('authors', 'authors.id', '=', 'books.author_id')
                ->groupBy('authors.id', 'authors.name')
                ->get()
                ->map(function ($a) {
                    $recent = $a->recent_avg ?? 0;
                    $last = $a->last_avg ?? 0;
                    $diff = $recent - $last;
                    $a->trending_score = round($diff * ($a->total_votes / 100), 2);
                    return $a;
                })
                ->sortByDesc('trending_score')
                ->take(20)
                ->values();
        }

        foreach ($authors as $a) {
            $a->best_book = Book::where('author_id', $a->id)
                ->withAvg('ratings as avg_rating', 'score')
                ->orderByDesc('avg_rating')
                ->first();

            $a->worst_book = Book::where('author_id', $a->id)
                ->withAvg('ratings as avg_rating', 'score')
                ->orderBy('avg_rating')
                ->first();
        }

        return view('authors.top-authors', compact('authors', 'tab'));
    }
}
