@extends('layout')

@section('content')
<div class="container py-4">

    {{-- Header Halaman --}}
    <h2 class="mb-4 fw-semibold" style="color: #4b2e05;">🏆 Top 20 Penulis</h2>

    {{-- Tab Navigasi --}}
    <ul class="nav nav-pills mb-4 gap-2" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link @if($tab == 'popularity') active @endif"
                style="
                   background-color: {{ $tab == 'popularity' ? '#8b5e34' : '#f3ede6' }};
                   color: {{ $tab == 'popularity' ? '#fff' : '#4b2e05' }};
                   border-radius: 20px;
                   font-weight: 500;
                   padding: 0.5rem 1rem;
               "
                href="{{ route('authors.index', ['tab' => 'popularity']) }}">
                Berdasarkan Popularitas
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link @if($tab == 'rating') active @endif"
                style="
                   background-color: {{ $tab == 'rating' ? '#8b5e34' : '#f3ede6' }};
                   color: {{ $tab == 'rating' ? '#fff' : '#4b2e05' }};
                   border-radius: 20px;
                   font-weight: 500;
                   padding: 0.5rem 1rem;
               "
                href="{{ route('authors.index', ['tab' => 'rating']) }}">
                Berdasarkan Rata-rata Rating
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link @if($tab == 'trending') active @endif"
                style="
                   background-color: {{ $tab == 'trending' ? '#8b5e34' : '#f3ede6' }};
                   color: {{ $tab == 'trending' ? '#fff' : '#4b2e05' }};
                   border-radius: 20px;
                   font-weight: 500;
                   padding: 0.5rem 1rem;
               "
                href="{{ route('authors.index', ['tab' => 'trending']) }}">
                Sedang Trending
            </a>
        </li>
    </ul>

    {{-- Tabel Data Penulis --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background-color: #f3ede6; color: #4b2e05;">
                        <tr class="text-uppercase small fw-semibold">
                            <th style="width: 5%;">#</th>
                            <th>Penulis</th>
                            <th class="text-center">Total Rating</th>
                            <th class="d-none d-md-table-cell">Buku Terbaik</th>
                            <th class="d-none d-lg-table-cell">Buku Terburuk</th>
                            @if($tab == 'trending')
                            <th class="text-center">Skor Trending</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($authors as $author)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold" style="color: #4b2e05;">{{ $author->nama }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background-color: #8b5e34; color: #fff;">
                                    {{ $author->total_ratings }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell text-muted">
                                {{ $author->best_book->judul ?? '-' }}
                            </td>
                            <td class="d-none d-lg-table-cell text-muted">
                                {{ $author->worst_book->judul ?? '-' }}
                            </td>
                            @if($tab == 'trending')
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background-color: #b86f3f; font-size: 0.95rem;">
                                    {{ number_format($author->trending_score, 2) }}
                                </span>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection