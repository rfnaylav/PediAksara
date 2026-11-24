@extends('layouts.app')

@section('title', $book->exists ? 'Ubah Buku' : 'Tambah Buku')

@section('content')
<div class="mb-6">
    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold mb-4" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar buku
    </a>
    <h1 class="text-2xl font-bold" style="color: #111827;">{{ $book->exists ? 'Ubah Buku' : 'Tambah Buku' }}</h1>
</div>

<div class="max-w-2xl bg-white rounded-2xl border shadow-sm p-6" style="border-color: #f3f4f6;">
    @if ($categories->isEmpty() || $authors->isEmpty() || $publishers->isEmpty())
        <div class="flex items-center gap-3 mb-5 p-4 rounded-xl" style="background: #fff7ed; border: 1px solid #fed7aa;">
            <svg class="w-5 h-5 flex-shrink-0" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-sm" style="color: #92400e;">Tambahkan kategori, penulis, dan penerbit terlebih dahulu.</p>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        @if ($book->exists) @method('PUT') @endif

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Judul Buku</label>
            <input name="title" value="{{ old('title', $book->title) }}" required maxlength="255" class="form-input" placeholder="Judul buku">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Sampul Buku</label>
            <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input">
            <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, atau WebP; maksimal 2 MB. Kosongkan jika tidak ingin mengganti sampul.</p>
            @if ($book->cover_image)
                <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Sampul {{ $book->title }}" class="mt-3 h-40 rounded-xl border object-cover" style="border-color: #ede9fe;">
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Kategori</label>
            <select name="category_id" required class="form-input">
                <option value="">— Pilih kategori —</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Penulis</label>
            <select name="author_id" required class="form-input">
                <option value="">— Pilih penulis —</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Penerbit</label>
            <select name="publisher_id" required class="form-input">
                <option value="">— Pilih penerbit —</option>
                @foreach ($publishers as $publisher)
                    <option value="{{ $publisher->id }}" @selected(old('publisher_id', $book->publisher_id) == $publisher->id)>{{ $publisher->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">ISBN</label>
            <input name="isbn" value="{{ old('isbn', $book->isbn) }}" maxlength="255" class="form-input" placeholder="ISBN (opsional)">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Tahun Terbit</label>
            <input type="number" name="year" value="{{ old('year', $book->year) }}" min="1000" max="{{ now()->year }}" class="form-input" placeholder="Tahun terbit">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', $book->stock ?? 0) }}" min="0" required class="form-input" placeholder="0">
        </div>

        <div class="sm:col-span-2 flex gap-3 pt-2">
            <button @disabled($categories->isEmpty() || $authors->isEmpty() || $publishers->isEmpty()) type="submit" class="btn-primary disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan
            </button>
            <a href="{{ route('books.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
