<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Toko Buku Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font modern elegan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --brown-dark: #4b2e05;
            --brown-medium: #8b5e34;
            --brown-light: #d4b996;
            --offwhite: #fdfcf9;
            --cream: #fff7ec;
            --text-dark: #2e2e2e;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--offwhite);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* === NAVBAR === */
        .navbar-custom {
            background: linear-gradient(90deg, var(--brown-dark), var(--brown-medium));
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
            letter-spacing: 0.5px;
            font-size: 1.5rem;
        }

        .navbar-custom .nav-link {
            color: #f8ede3 !important;
            font-weight: 500;
            margin: 0 0.6rem;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--brown-light) !important;
            border-bottom: 2px solid var(--brown-light);
        }

        /* === CONTENT BOX === */
        .content-box {
            background: var(--cream);
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            padding: 2rem 2.5rem;
            transition: all 0.3s ease;
        }

        .content-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        /* === BUTTON === */
        .btn-brown {
            background-color: var(--brown-medium);
            border: none;
            color: #fff;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.3rem;
            transition: all 0.3s ease;
        }

        .btn-brown:hover {
            background-color: var(--brown-dark);
            transform: translateY(-1px);
        }

        .btn-outline-brown {
            color: var(--brown-dark);
            border: 1px solid var(--brown-medium);
            background-color: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-brown:hover {
            background-color: var(--brown-medium);
            color: #fff;
        }

        /* === ALERT === */
        .alert-success {
            background-color: #f8f2ea;
            color: var(--brown-dark);
            border: 1px solid var(--brown-light);
            font-weight: 500;
        }

        /* === FOOTER === */
        footer {
            background: linear-gradient(90deg, var(--brown-dark), var(--brown-medium));
            color: #fdfcf9;
            text-align: center;
            padding: 1.2rem 0;
            font-size: 0.9rem;
            margin-top: 3rem;
            letter-spacing: 0.3px;
        }

        footer span {
            color: var(--brown-light);
        }

        /* Responsif padding */
        @media (max-width: 768px) {
            .content-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">📖 Toko Buku</a>

            <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->is('books*') ? 'active' : '' }}">Buku</a>
                    <a href="{{ route('authors.index') }}" class="nav-link {{ request()->is('authors*') ? 'active' : '' }}">Penulis</a>
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">Kategori</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="content-box">
            @yield('content')
        </div>
    </div>

    <footer>
        © {{ date('Y') }} <span>Toko Buku Modern</span> • Dibuat dengan ☕ oleh Laravel
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>