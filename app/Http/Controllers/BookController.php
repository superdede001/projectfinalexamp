<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
{
    // ========================== BASE QUERY ====================================
    $query = Book::query()
        ->leftJoin('ratings', 'ratings.book_id', '=', 'books.id')
        ->select(
            'books.id',
            'books.title',
            'books.author_id',
            'books.category_id',
            'books.isbn',
            'books.publisher',
            'books.publication_year',
            'books.store_location',
            'books.availability_status',
            'books.created_at',
            'books.updated_at',
            DB::raw('COALESCE(AVG(ratings.score), 0) as avg_rating'),
            DB::raw('COUNT(ratings.id) as total_voters')
        )
        ->groupBy(
            'books.id',
            'books.title',
            'books.author_id',
            'books.category_id',
            'books.isbn',
            'books.publisher',
            'books.publication_year',
            'books.store_location',
            'books.availability_status',
            'books.created_at',
            'books.updated_at'
        );

    // ========================== FILTER ========================================
    if ($request->filled('author_id')) {
        $query->where('books.author_id', $request->author_id);
    }

    if ($request->filled('categories')) {
        $categories = array_filter($request->categories);
        if (!empty($categories)) {
            $query->whereIn('books.category_id', $categories);
        }
    }

    if ($request->filled('year_start') && $request->filled('year_end')) {
        $query->whereBetween('books.publication_year', [$request->year_start, $request->year_end]);
    }

    if ($request->filled('store_location')) {
        $query->where('books.store_location', $request->store_location);
    }

    if ($request->filled('status')) {
        $query->where('books.availability_status', $request->status);
    }

    if ($request->filled('rating_min') && $request->filled('rating_max')) {
        $query->havingBetween('avg_rating', [$request->rating_min, $request->rating_max]);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('books.title', 'like', "%$search%")
              ->orWhere('books.isbn', 'like', "%$search%")
              ->orWhere('books.publisher', 'like', "%$search%");
        });
    }

    // ========================== SORTING =======================================
    $tab = $request->get('tab', 'popularity');

    switch ($tab) {
        case 'average':
            $query->orderByDesc('avg_rating');
            break;
        default:
            $query->orderByDesc('total_voters');
            break;
    }

    // ========================== GET DATA ======================================
    $books = $query->paginate(20)->appends($request->query());

    // ================== RECENT AVG SEPARATE QUERY =============================
    $recentAverages = DB::table('ratings')
        ->select('book_id', DB::raw('AVG(score) as recent_avg'))
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('book_id')
        ->pluck('recent_avg', 'book_id');

    $books->getCollection()->transform(function ($book) use ($recentAverages) {
        $book->recent_avg = $recentAverages[$book->id] ?? $book->avg_rating;
        $book->trending_diff = $book->recent_avg - $book->avg_rating;
        return $book;
    });

    //============================== DROPDOWN ===================================
    $authors = Author::select('id', 'name')->get();
    $categories = Category::select('id', 'name')->get();

    return view('books.index', compact('books', 'authors', 'categories', 'tab'));
}


}
