@extends('layout')

@section('content')
<div class="container py-4">

    {{-- FILTER FORM --}}
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px; background-color: #fff;">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                {{-- Pencarian --}}
                <div class="col-md-4 col-lg-3">
                    <label for="keyword" class="form-label text-muted small">Cari (Judul/ISBN/Penulis/Penerbit)</label>
                    <input type="text" name="keyword" id="keyword" class="form-control"
                        placeholder="Search books..." value="{{ request('keyword') }}">
                </div>

                {{-- Penulis --}}
                <div class="col-md-4 col-lg-3">
                    <label for="author_id" class="form-label text-muted small">Penulis</label>
                    <select name="author_id" id="author_id" class="form-select">
                        <option value="">Semua Penulis</option>
                        @foreach ($authors as $author)
                        <option value="{{ $author->id }}" @selected(request('author_id')==$author->id)>
                            {{ $author->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Urutkan --}}
                <div class="col-md-4 col-lg-3">
                    <label for="sort" class="form-label text-muted small">Urutkan Berdasarkan</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="weighted" @selected(request('sort', 'weighted' )=='weighted' )>Default (Rekomendasi)</option>
                        <option value="rating" @selected(request('sort')=='rating' )>Rating Tertinggi</option>
                        <option value="votes" @selected(request('sort')=='votes' )>Total Votes</option>
                        <option value="popularity" @selected(request('sort')=='popularity' )>Popularitas Terbaru</option>
                        <option value="alphabetical" @selected(request('sort')=='alphabetical' )>Judul A-Z</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-md-6 col-lg-3 d-grid gap-2 order-md-last">
                    <button class="btn btn-brown">Terapkan Filter</button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                </div>

                <div class="col-12">
                    <hr class="my-2">
                </div>

                {{-- Kategori --}}
                <div class="col-md-6 col-lg-3">
                    <label for="categories" class="form-label text-muted small">Kategori (Pilih banyak)</label>
                    <select name="categories[]" id="categories" multiple class="form-select" size="3">
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(in_array($c->id, (array) request('categories', [])))>
                            {{ $c->nama }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-1 d-block">
                        <label class="me-3"><input type="radio" name="mode" value="or" @checked(request('mode', 'or' )=='or' )>
                            OR (Salah Satu)</label>
                        <label><input type="radio" name="mode" value="and" @checked(request('mode')=='and' )>
                            AND (Semua)</label>
                    </small>
                </div>

                {{-- Rating --}}
                <div class="col-md-6 col-lg-3">
                    <label for="rating_min" class="form-label text-muted small">Rating Minimum/Maksimum</label>
                    <div class="input-group">
                        <input type="number" name="rating_min" placeholder="Min" class="form-control" min="1" max="10"
                            value="{{ request('rating_min') }}">
                        <input type="number" name="rating_max" placeholder="Max" class="form-control" min="1" max="10"
                            value="{{ request('rating_max') }}">
                    </div>
                </div>

                {{-- Status & Lokasi --}}
                <div class="col-md-6 col-lg-3">
                    <label for="availability_status" class="form-label text-muted small">Status & Lokasi</label>
                    <div class="d-flex gap-2">
                        <select name="availability_status" id="availability_status" class="form-select w-50">
                            <option value="">Semua Status</option>
                            @foreach($availabilityStatuses as $status)
                            <option value="{{ $status }}" @selected(request('availability_status')==$status)>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                        <select name="store_location" id="store_location" class="form-select w-50">
                            <option value="">Semua Lokasi</option>
                            @foreach($storeLocations as $location)
                            <option value="{{ $location }}" @selected(request('store_location')==$location)>
                                {{ $location }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tahun Terbit --}}
                <div class="col-md-6 col-lg-3">
                    <label for="year_from" class="form-label text-muted small">Tahun Terbit</label>
                    <div class="input-group">
                        <input type="number" name="year_from" placeholder="Dari Tahun" class="form-control"
                            value="{{ request('year_from') }}">
                        <input type="number" name="year_to" placeholder="Sampai Tahun" class="form-control"
                            value="{{ request('year_to') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DAFTAR BUKU --}}
    <h3 class="mt-4 mb-3 fw-semibold" style="color: #4b2e05;">
        Daftar Buku <span class="badge" style="background-color:#d4b996; color:#4b2e05;">Total: {{ $books->total() }}</span>
    </h3>

    {{-- TABEL BUKU --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="border-radius: 10px; overflow: hidden;">
            <thead style="background-color: #8b5e34; color: #fff;">
                <tr class="text-uppercase small">
                    <th style="width: 5%">#</th>
                    <th>Judul</th>
                    <th class="d-none d-lg-table-cell">Penulis</th>
                    <th>Kategori</th>
                    <th class="text-center d-none d-md-table-cell">Tahun</th>
                    <th class="text-center">Rating</th>
                    <th class="text-center d-none d-md-table-cell">Popularitas</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody style="background-color: #fff;">
                @foreach($books as $book)
                <tr style="border-bottom: 1px solid #f1e8dd;">
                    <td>{{ $books->firstItem() + $loop->index }}</td>
                    <td>
                        <span class="fw-semibold" style="color:#4b2e05;">{{ $book->judul }}</span>
                        @if($book->trending_status)
                        <span class="badge bg-danger">HOT</span>
                        @endif
                        <div class="text-muted small d-lg-none">{{ $book->author->nama ?? '-' }}</div>
                        <div class="text-muted small d-none d-md-block">ISBN: {{ $book->isbn }}</div>
                    </td>
                    <td class="d-none d-lg-table-cell">{{ $book->author->nama ?? '-' }}</td>
                    <td>
                        @forelse($book->categories as $cat)
                        <span class="badge" style="background-color:#b08968; color:#fff;">{{ $cat->nama }}</span>
                        @empty
                        -
                        @endforelse
                    </td>
                    <td class="text-center d-none d-md-table-cell">{{ $book->tahun_publis ?? '-' }}</td>
                    <td class="text-center">
                        <span class="fw-bold" style="color:#8b5e34;">{{ $book->avg_rating ?? '-' }}</span>
                        <div class="text-muted small">({{ $book->votes_count }})</div>
                    </td>
                    <td class="text-center d-none d-md-table-cell">{{ $book->recent_popularity_score ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background-color:#d4b996; color:#4b2e05;">
                            {{ ucfirst($book->availability_status ?? '-') }}
                        </span>
                        <div class="text-muted small">{{ $book->store_location ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        @if($book->canRate)
                        <a href="{{ route('ratings.create', $book->id) }}" class="btn btn-brown btn-sm">Rate</a>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>Sudah dirating</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $books->appends(request()->except('page'))->links() }}
    </div>
</div>
@endsection