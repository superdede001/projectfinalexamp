<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;


class BookController extends Controller
{
    /**
     * Tampilkan daftar buku dengan filter lengkap dan pagination
     */
    public function index(Request $request)
    {
        // 1. Query awal dengan eager load
        $query = Book::query()->with(['author', 'categories']);

        // 2. Terapkan filter & search
        $this->applyFilters($query, $request);

        // 3. Sorting
        $this->applySorting($query, $request);

        // 4. Pagination
        $books = $query->paginate(20);

        foreach ($books as $book) {
            $book->canRate = !Rating::where('book_id', $book->id)
                ->when(auth()->check(), fn($q) => $q->where('user_id', auth()->id()))
                ->when(!auth()->check(), fn($q) => $q->where('guest_ip', request()->ip()))
                ->where('created_at', '>', now()->subHours(24))
                ->exists();
            $colors = [
                'available' => 'text-green-500',
                'unavailable' => 'text-red-500',
                'pending' => 'text-yellow-500',
            ];

            $book->statusColor = $colors[$book->availability_status] ?? 'text-gray-500';
        }
        // 5. Tambahkan trending flag
        $this->addTrendingFlag($books);

        // 6. Siapkan data dropdown
        $authors = Author::orderBy('nama')->get();
        $categories = Category::orderBy('nama')->get();
        $availabilityStatuses = Book::select('availability_status')->distinct()->pluck('availability_status');
        $storeLocations = Book::select('store_location')->distinct()->pluck('store_location');

        return view('books.index', compact(
            'books',
            'authors',
            'categories',
            'availabilityStatuses',
            'storeLocations'
        ));
    }

    /**
     * Filter & search logic
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        // Keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('isbn', 'like', "%{$keyword}%")
                    ->orWhere('publisher', 'like', "%{$keyword}%")
                    ->orWhereHas('author', fn($a) => $a->where('nama', 'like', "%{$keyword}%"));
            });
        }

        // Categories
        if ($request->filled('categories')) {
            $catIds = (array) $request->categories;
            $mode = $request->get('mode', 'or');

            $query->whereHas('categories', function (Builder $c) use ($catIds, $mode) {
                if ($mode === 'and') {
                    foreach ($catIds as $catId) {
                        $c->where('categories.id', $catId);
                    }
                } else {
                    $c->whereIn('categories.id', $catIds);
                }
            });
        }

        // Single filters
        if ($request->filled('author_id'))
            $query->where('author_id', $request->author_id);
        if ($request->filled('availability_status'))
            $query->where('availability_status', $request->availability_status);
        if ($request->filled('store_location'))
            $query->where('store_location', $request->store_location);

        // Tahun terbit range
        if ($request->filled('year_from') || $request->filled('year_to')) {
            $query->whereBetween('tahun_publis', [
                $request->get('year_from', 0),
                $request->get('year_to', now()->year),
            ]);
        }

        // Rating range
        if ($request->filled('rating_min') || $request->filled('rating_max')) {
            $query->whereBetween('avg_rating', [
                $request->get('rating_min', 1),
                $request->get('rating_max', 10),
            ]);
        }
    }

    /**
     * Sorting logic
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        switch ($request->get('sort', 'weighted')) {
            case 'votes':
                $query->orderByDesc('votes_count');
                break;
            case 'rating':
                $query->orderByDesc('avg_rating');
                break;
            case 'popularity':
                $query->orderByDesc('recent_popularity_score');
                break;
            case 'alphabetical':
                $query->orderBy('judul');
                break;
            default:
                $query->orderByRaw('(avg_rating * votes_count) DESC');
        }
    }

    /**
     * Tambahkan trending flag (↑ jika rating naik dalam 7 hari terakhir, ↓ jika turun)
     */
    protected function addTrendingFlag($books): void
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $books->getCollection()->each(function ($book) use ($sevenDaysAgo) {
            $book->trending_status = '';

            if ($book->updated_at > $sevenDaysAgo) {
                if ($book->avg_rating >= 8) {
                    $book->trending_status = '↑';
                } elseif ($book->avg_rating < 4) {
                    $book->trending_status = '↓';
                }
            }
        });
    }
}
