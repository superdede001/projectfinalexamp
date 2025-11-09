@extends('layout')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #4b2e05;">📚 Daftar Kategori Buku</h2>
        <span class="badge" style="background-color: #d4b996; color: #4b2e05; font-size: 0.95rem;">
            Total {{ count($categories) }} Kategori
        </span>
    </div>

    {{-- CARD WRAPPER --}}
    <div class="card shadow-sm border-0" style="border-radius: 14px; background-color: #fff8f0;">

        {{-- HEADER CARD --}}
        <div class="card-header border-0"
            style="background-color: #8b5e34; color: #fff; border-radius: 14px 14px 0 0;">
            <h5 class="mb-0 fw-semibold">✨ Jelajahi Dunia Buku Berdasarkan Kategori</h5>
        </div>

        {{-- LIST GROUP --}}
        <ul class="list-group list-group-flush">
            @forelse($categories as $c)
            <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                style="border: none; background-color: #fff; transition: all 0.3s;">

                <div>
                    <span class="fw-bold fs-5" style="color: #4b2e05;">
                        {{ $c->nama }}
                    </span>
                    <div class="text-muted small mt-1">Kategori populer untuk berbagai jenis buku 📖</div>
                </div>

                <div>
                    <span class="badge rounded-pill"
                        style="background-color: #b08968; color: #fff; padding: 0.6em 1.2em;">
                        {{ $c->books_count }} Buku
                    </span>
                </div>
            </li>
            <hr class="my-0 mx-3" style="border-color: #f0e5d8;">
            @empty
            <li class="list-group-item text-center text-muted py-5" style="background-color: #fff;">
                Tidak ada kategori yang terdaftar saat ini.
            </li>
            @endforelse
        </ul>
    </div>

    {{-- FOOTER DECORATION --}}
    <div class="text-center mt-4">
        <small class="text-muted">
            🌿 Temukan kategori yang sesuai dengan minat bacamu — dari fiksi klasik hingga ilmu pengetahuan modern.
        </small>
    </div>

</div>
@endsection