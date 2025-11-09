<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RatingController extends Controller
{
    public function create()
    {
        $authors = Author::select('id', 'name')->orderBy('name')->get();
        return view('ratings.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'author_id' => 'required|exists:authors,id',
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:1|max:10',
        ]);

        $book = Book::findOrFail($request->book_id);
        if ($book->author_id != $request->author_id) {
            return back()->withErrors(['book_id' => 'Buku ini bukan milik author tersebut!']);
        }


        $existing = Rating::where('book_id', $request->book_id)
            ->where('user_id', $request->user_id)
            ->first();
        if ($existing) {
            return back()->withErrors(['score' => 'Kamu sudah pernah memberi rating untuk buku ini!']);
        }

        $last = Rating::where('user_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($last && $last->created_at->gt(Carbon::now()->subHours(24))) {
            return back()->withErrors(['user_id' => 'Kamu harus menunggu 24 jam sebelum memberi rating lagi.']);
        }

        DB::transaction(function () use ($request) {
            Rating::create([
                'book_id' => $request->book_id,
                'user_id' => $request->user_id,
                'score' => $request->score,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect('/books')->with('success', 'Rating berhasil disimpan!');
    }
}
