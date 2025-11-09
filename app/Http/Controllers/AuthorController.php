<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Author;

class AuthorController extends Controller
{
    public function topAuthors()
    {
        $authors = Author::select('authors.*', 
                DB::raw('AVG(ratings.score) as avg_rating'),
                DB::raw('COUNT(ratings.id) as total_ratings')
            )
            ->join('books', 'books.author_id', '=', 'authors.id')
            ->join('ratings', 'ratings.book_id', '=', 'books.id')
            ->groupBy('authors.id')
            ->orderByDesc('avg_rating')
            ->limit(10)
            ->get();

        return view('books.top-authors', compact('authors'));
    }
}
