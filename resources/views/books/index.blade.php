@extends('layouts.app')

@section('title', 'Books List')

@section('content')
<style>
    /* === TABLE STYLING === */
    table.table {
        border: none;
        border-collapse: collapse;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fdfdfd;
    }
    table.table th,
    table.table td {
        border-bottom: 1px solid #e0e0e0;
        vertical-align: middle;
        padding: 12px 16px;
    }
    table.table thead {
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    table.table tbody tr {
        transition: background-color 0.3s ease;
    }
    table.table tbody tr:hover {
        background-color: #f1f5f9;
    }
    table.table td span {
        display: inline-block;
        margin-right: 4px;
    }

    /* === NAVBAR STYLING === */
    .navbar {
        background: linear-gradient(90deg, #1c1c1c, #3a3a3a);
        box-shadow: 0 3px 12px rgba(0,0,0,0.2);
        border-radius: 8px;
    }
    .navbar-brand {
        font-weight: 700;
        color: #f8e1a1 !important;
        letter-spacing: 1px;
        font-size: 1.3rem;
    }
    .navbar-nav .nav-link {
        color: #e0e0e0 !important;
        font-weight: 500;
        padding: 10px 16px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }
    .navbar-nav .nav-link:hover {
        background-color: rgba(248,225,161,0.2);
        color: #f8e1a1 !important;
    }
    .navbar-nav .nav-link.active {
        background-color: #f8e1a1;
        color: #1c1c1c !important;
        font-weight: 600;
    }

    /* === FORM INPUTS === */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ccc;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4b6cb7;
        box-shadow: 0 0 8px rgba(75,108,183,0.3);
    }

    /* === BUTTONS === */
    .btn-success {
        background-color: #4b6cb7;
        border-color: #4b6cb7;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    .btn-success:hover {
        background-color: #182848;
        border-color: #182848;
        box-shadow: 0 4px 10px rgba(24,40,72,0.3);
    }
    .btn-outline-primary {
        border-color: #182848;
        color: #182848;
        transition: all 0.3s ease;
    }
    .btn-outline-primary:hover {
        background-color: #182848;
        color: #fdfdfd;
        box-shadow: 0 4px 10px rgba(24,40,72,0.3);
    }
    .btn-outline-warning {
        border-color: #f8e1a1;
        color: #182848;
        transition: all 0.3s ease;
    }
    .btn-outline-warning:hover {
        background-color: #f8e1a1;
        color: #182848;
        box-shadow: 0 4px 10px rgba(248,225,161,0.3);
    }

    /* === HEADER === */
    h2 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
        color: #182848;
        letter-spacing: 0.5px;
    }

    /* === BADGE STATUS === */
    .badge {
        font-weight: 600;
        padding: 0.5em 0.7em;
        border-radius: 0.5rem;
    }
    .bg-success { background-color: #2ecc71 !important; }
    .bg-danger { background-color: #e74c3c !important; }
    .bg-warning { background-color: #f1c40f !important; color: #182848 !important; }
</style>


<h2 class="mb-4 fw-bold text-primary">📚 List of Books</h2>

<form method="GET" action="/books" class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-semibold">🔍 Search</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search title...">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">👤 Author</label>
            <select name="author_id" class="form-select">
                <option value="">All</option>
                @foreach($authors as $a)
                    <option value="{{ $a->id }}" {{ request('author_id') == $a->id ? 'selected' : '' }}>
                        {{ $a->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">🏷️ Category</label>
            <select name="categories[]" class="form-select">
                <option value="">All</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ in_array($c->id, request('categories', [])) ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">📅 Year</label>
            <div class="input-group">
                <input type="number" name="year_start" class="form-control" placeholder="From" value="{{ request('year_start') }}">
                <input type="number" name="year_end" class="form-control" placeholder="To" value="{{ request('year_end') }}">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">🏬 Store</label>
            <input type="text" name="store_location" class="form-control" value="{{ request('store_location') }}" placeholder="Location...">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">⭐ Rating</label>
            <div class="input-group">
                <input type="number" name="rating_min" class="form-control" placeholder="Min" min="1" max="10" value="{{ request('rating_min') }}">
                <input type="number" name="rating_max" class="form-control" placeholder="Max" min="1" max="10" value="{{ request('rating_max') }}">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">📖 Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">↕️ Sort by</label>
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="">None</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Weighted Rating</option>
                <option value="votes" {{ request('sort') == 'votes' ? 'selected' : '' }}>Total Votes</option>
                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity (30d)</option>
                <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100 fw-semibold">Search</button>
        </div>
    </div>
</form>

<!-- Notif -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
         {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
         <strong>Oops!</strong> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- TABLE -->
<div class="table-responsive shadow-sm rounded-3 border">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-primary text-center">
            <tr>
                <th class="py-3">📘 Title</th>
                <th>👤 Author</th>
                <th>🏷️ Category</th>
                <th>📅 Year</th>
                <th>🏬 Store</th>
                <th>⭐ Rating</th>
                <th>🧮 Voters</th>
                <th>📖 Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
                <tr class="text-center">
                    <td class="text-start fw-semibold">{{ $book->title }}</td>
                    <td>{{ $book->author->name ?? '-' }}</td>
                    <td>{{ $book->category->name ?? '-' }}</td>
                    <td>{{ $book->publication_year }}</td>
                    <td>{{ $book->store_location }}</td>
                    <td>
                        <span class="fw-semibold">
                            {{ number_format($book->avg_rating, 2) }}
                        </span>
                        @if($book->trending_diff > 0)
                            <span class="text-success fw-bold ms-1" title="Trending up!">↑</span>
                        @elseif($book->trending_diff < 0)
                            <span class="text-danger fw-bold ms-1" title="Trending down!">↓</span>
                        @else
                            <span class="text-secondary ms-1">–</span>
                        @endif
                    </td>
                    <td>{{ $book->total_voters }}</td>
                    <td>
                        <span class="badge 
                            @if($book->status == 'available') bg-success
                            @elseif($book->status == 'rented') bg-danger
                            @else bg-warning text-dark @endif">
                            {{ ucfirst($book->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No books found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="mt-4 d-flex justify-content-center">
    {{ $books->links() }}
</div>

<!-- ACTION BUTTONS -->
<div class="d-flex gap-3 mt-4 justify-content-center">
    <a href="/authors/top-authors" class="btn btn-outline-primary d-flex align-items-center gap-2 px-4">
        <i class="bi bi-trophy-fill"></i> Top Authors
    </a>
    <a href="/ratings/create" class="btn btn-outline-warning d-flex align-items-center gap-2 px-4">
        <i class="bi bi-star-fill text-warning"></i> Add Rating
    </a>
</div>
@endsection
