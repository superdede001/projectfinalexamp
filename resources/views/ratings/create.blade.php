@extends('layout')

@section('content')
<h3>Beri Rating untuk "{{ $book->judul }}"</h3>

<form method="POST" action="{{ route('ratings.store') }}">
    @csrf
    <input type="hidden" name="book_id" value="{{ $book->id }}">
    {{-- Menyimpan URL asal --}}
    <input type="hidden" name="redirect_to" value="{{ url()->previous() }}">

    <div class="mb-3">
        <label class="form-label">Nilai (1-10)</label>
        <select name="score" class="form-select @error('score') is-invalid @enderror" required>
            <option value="">Pilih rating</option>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" @selected(old('score')==$i)>{{ $i }}</option>
                @endfor
        </select>
        @error('score')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Komentar (Opsional)</label>
        <textarea name="comment" class="form-control @error('comment') is-invalid @enderror"
            rows="3">{{ old('comment') }}</textarea>
        @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-success">Kirim</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection