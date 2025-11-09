@extends('layouts.app')

@section('title', 'Add Rating')

@section('content')
<style>
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

    /* === FORM CONTAINER === */
    form {
        background-color: #fdfdfd;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
        max-width: 700px;
        margin: auto;
    }

    h2 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
        color: #182848;
        letter-spacing: 0.5px;
        text-align: center;
        margin-bottom: 2rem;
    }

    /* === INPUT & SELECT === */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ccc;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4b6cb7;
        box-shadow: 0 0 8px rgba(75,108,183,0.3);
    }

    /* === ALERTS === */
    .alert {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 1rem 1.25rem;
    }
    .alert-danger {
        background-color: #fdecea;
        color: #b71c1c;
        border: 1px solid #f5c6cb;
    }

    /* === BUTTONS === */
    .btn-primary {
        background-color: #4b6cb7;
        border-color: #4b6cb7;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #182848;
        border-color: #182848;
        box-shadow: 0 4px 10px rgba(24,40,72,0.3);
    }
    .btn-outline-secondary {
        border-color: #182848;
        color: #182848;
        transition: all 0.3s ease;
    }
    .btn-outline-secondary:hover {
        background-color: #182848;
        color: #fdfdfd;
        box-shadow: 0 4px 10px rgba(24,40,72,0.3);
    }

    /* === FORM LABELS === */
    .form-label {
        font-weight: 600;
        color: #182848;
    }

    /* === FORM TEXT === */
    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
<h2 class="mb-4">⭐ Add New Rating</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Oops!</strong> Please fix the following issues:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('ratings.store') }}" class="p-4 bg-white shadow-sm rounded-3 border">
    @csrf

    <!-- AUTHOR -->
    <div class="mb-3">
        <label for="author_id" class="form-label fw-semibold">👤 Author</label>
        <select id="author_id" name="author_id" class="form-select" required>
            <option value="">-- Select Author --</option>
            @foreach ($authors as $author)
                <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                    {{ $author->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- BOOK (auto-loaded by author) -->
    <div class="mb-3">
        <label for="book_id" class="form-label fw-semibold">📚 Book</label>
        <select id="book_id" name="book_id" class="form-select" required disabled>
            <option value="">Select author first...</option>
        </select>
    </div>

    <!-- USER -->
    <div class="mb-3">
        <label for="user_id" class="form-label fw-semibold">🙋‍♂️ User</label>
        <select id="user_id" name="user_id" class="form-select" required>
            @for ($i = 1; $i <= 5000; $i++)
                <option value="{{ $i }}" {{ old('user_id') == $i ? 'selected' : '' }}>
                    User {{ $i }}
                </option>
            @endfor
        </select>
        <div class="form-text">* Simulasi user dari 1–5000</div>
    </div>

    <!-- RATING -->
    <div class="mb-4">
        <label for="score" class="form-label fw-semibold">⭐ Rating (1–10)</label>
        <select id="score" name="score" class="form-select" required>
            @for ($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" {{ old('score') == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>

    <!-- SUBMIT -->
    <div class="d-flex justify-content-between">
        <a href="/books" class="btn btn-outline-secondary">
            ⬅ Back to Books
        </a>
        <button type="submit" class="btn btn-primary fw-semibold px-4">
            💾 Submit Rating
        </button>
    </div>
</form>

<!-- JS for dependent dropdown -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const authorSelect = document.getElementById('author_id');
    const bookSelect = document.getElementById('book_id');

    authorSelect.addEventListener('change', function () {
        const authorId = this.value;
        bookSelect.innerHTML = '<option>Loading books...</option>';
        bookSelect.disabled = true;

        if (authorId) {
            fetch(`/api/author/${authorId}/books`)
                .then(response => response.json())
                .then(data => {
                    bookSelect.innerHTML = '<option value="">-- Select Book --</option>';
                    data.forEach(book => {
                        const option = document.createElement('option');
                        option.value = book.id;
                        option.textContent = book.title;
                        bookSelect.appendChild(option);
                    });
                    bookSelect.disabled = false;
                })
                .catch(() => {
                    bookSelect.innerHTML = '<option>Error loading books</option>';
                });
        } else {
            bookSelect.innerHTML = '<option>Select author first...</option>';
        }
    });
});
</script>
@endsection
