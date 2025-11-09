@extends('layout')

@section('content')
<div class="container py-5">

    {{-- HEADER UTAMA --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="color: #4b2e05;">📖 Selamat Datang di Toko Buku Modern</h1>
        <p class="text-muted fs-5 mt-3" style="max-width: 600px; margin: auto;">
            Temukan ribuan buku dari berbagai penulis hebat.
            Nilai, ulas, dan jelajahi karya favoritmu dengan tampilan baru yang lebih elegan.
        </p>
    </div>

    {{-- FITUR UTAMA (Card Section) --}}
    <div class="row g-4 justify-content-center">

        {{-- Buku --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-radius: 14px; background-color: #fff8f0; transition: all 0.3s ease;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 fs-1">📚</div>
                    <h5 class="fw-semibold mb-2" style="color: #4b2e05;">Jelajahi Buku</h5>
                    <p class="text-muted small mb-3">
                        Lihat koleksi buku lengkap dengan fitur filter canggih dan rating pembaca.
                    </p>
                    <a href="{{ route('books.index') }}" class="btn btn-brown px-4">Lihat Buku</a>
                </div>
            </div>
        </div>

        {{-- Penulis --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-radius: 14px; background-color: #fffdf8; transition: all 0.3s ease;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 fs-1">✍️</div>
                    <h5 class="fw-semibold mb-2" style="color: #4b2e05;">Penulis Terbaik</h5>
                    <p class="text-muted small mb-3">
                        Temukan daftar penulis populer dan buku paling banyak mendapat rating tinggi.
                    </p>
                    <a href="{{ route('authors.index') }}" class="btn btn-brown px-4">Lihat Penulis</a>
                </div>
            </div>
        </div>

        {{-- Kategori --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-radius: 14px; background-color: #fff8f0; transition: all 0.3s ease;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 fs-1">🏷️</div>
                    <h5 class="fw-semibold mb-2" style="color: #4b2e05;">Kategori Buku</h5>
                    <p class="text-muted small mb-3">
                        Jelajahi buku berdasarkan kategori pilihan dan temukan genre favoritmu.
                    </p>
                    <a href="{{ route('categories.index') }}" class="btn btn-brown px-4">Lihat Kategori</a>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER SECTION (PROMO RINGAN) --}}
    <div class="text-center mt-5 pt-4">
        <h5 class="fw-semibold" style="color: #4b2e05;">✨ Nikmati Pengalaman Membaca yang Lebih Hangat</h5>
        <p class="text-muted small mt-2">
            Toko Buku Modern – Satu tempat untuk semua cerita, inspirasi, dan pengetahuan.
        </p>
    </div>

</div>
@endsection