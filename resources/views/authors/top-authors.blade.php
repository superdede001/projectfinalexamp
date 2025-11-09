@extends('layouts.app')

@section('title', 'Top 20 Authors')

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

    /* === TREND ICON === */
    .trend-up {
        color: #2ecc71; /* fresh green */
        font-weight: bold;
    }
    .trend-down {
        color: #e74c3c; /* soft red */
        font-weight: bold;
    }

    /* === NAVBAR STYLING === */
    .navbar {
        background: linear-gradient(90deg, #1c1c1c, #3a3a3a);
        box-shadow: 0 3px 12px rgba(0,0,0,0.2);
        border-radius: 8px;
    }
    .navbar-brand {
        font-weight: 700;
        color: #f8e1a1 !important; /* elegant gold */
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

    /* === HEADER === */
    h2 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
        color: #182848;
        letter-spacing: 0.5px;
    }

    /* === BACK LINK BUTTON === */
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
</style>


<h2 class="mb-4 fw-bold text-primary text-center">🏆 Top 20 Most Famous Authors</h2>

<!-- TAB NAVIGATION -->
<ul class="nav nav-pills justify-content-center mb-4">
    <li class="nav-item">
        <a href="?tab=popularity" class="nav-link {{ $tab === 'popularity' ? 'active' : '' }}">
            🔥 By Popularity
        </a>
    </li>
    <li class="nav-item">
        <a href="?tab=average" class="nav-link {{ $tab === 'average' ? 'active' : '' }}">
            ⭐ By Average Rating
        </a>
    </li>
    <li class="nav-item">
        <a href="?tab=trending" class="nav-link {{ $tab === 'trending' ? 'active' : '' }}">
            📈 Trending
        </a>
    </li>
</ul>

<!-- TABLE -->
<div class="table-responsive shadow-sm rounded-3 border">
    <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-primary text-center">
            <tr>
                <th>👤 Author</th>
                <th>⭐ Avg Rating</th>
                <th>🧮 Voters</th>
                <th>📈 Trending Score</th>
                <th>🏅 Best Book</th>
                <th>📉 Worst Book</th>
            </tr>
        </thead>
        <tbody>
            @forelse($authors as $a)
                <tr>
                    <td class="fw-semibold text-start">{{ $a->name }}</td>
                    <td>{{ number_format($a->avg_rating ?? $a->recent_avg ?? 0, 2) }}</td>
                    <td>{{ $a->voters_count ?? $a->total_votes ?? 0 }}</td>
                    <td>
                        @if(isset($a->trending_score))
                            @if($a->trending_score > 0)
                                <span class="trend-up">▲ +{{ number_format($a->trending_score, 2) }}</span>
                            @elseif($a->trending_score < 0)
                                <span class="trend-down">▼ {{ number_format($a->trending_score, 2) }}</span>
                            @else
                                <span class="text-secondary">0.00</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($a->best_book)
                            <span class="fw-semibold">{{ $a->best_book->title }}</span>
                            <span class="text-success small">
                                ({{ number_format($a->best_book->avg_rating, 2) }})
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($a->worst_book)
                            <span class="fw-semibold">{{ $a->worst_book->title }}</span>
                            <span class="text-danger small">
                                ({{ number_format($a->worst_book->avg_rating, 2) }})
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No authors found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- BACK LINK -->
<div class="mt-4 text-center">
    <a href="/books" class="btn btn-outline-primary px-4 fw-semibold">
        ⬅ Back to Books
    </a>
</div>
@endsection
