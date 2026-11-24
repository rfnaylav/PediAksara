@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<!-- Header -->
<div class="rounded-2xl mb-6 p-6 relative overflow-hidden" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-purple-200 text-sm font-medium mb-1">📚 Koleksi Perpustakaan</p>
            <h1 class="text-2xl font-bold text-white">Temukan Buku Favoritmu</h1>
            <p class="text-purple-200 text-sm mt-1">Jelajahi ribuan koleksi buku kami</p>
        </div>
        <form method="GET" action="{{ route('catalog.index') }}" class="flex w-full max-w-xl flex-col gap-2">
            <div class="flex gap-2">
                <input name="q" value="{{ request('q') }}" maxlength="100" placeholder="Cari judul, ISBN, penulis, kategori..." class="min-w-0 flex-1 rounded-xl px-4 py-2.5 text-sm text-gray-900 outline-none" style="background: white;">
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-purple-900" style="background: #c4b5fd;">Cari</button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <label class="text-xs font-medium text-purple-100">Kategori
                    <select name="category_id" class="mt-1 w-full rounded-lg border-0 px-3 py-2 text-sm text-gray-800">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-medium text-purple-100">Penulis
                    <select name="author_id" class="mt-1 w-full rounded-lg border-0 px-3 py-2 text-sm text-gray-800">
                        <option value="">Semua penulis</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" @selected((string) request('author_id') === (string) $author->id)>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-white">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="available" value="1" @checked(request()->boolean('available'))>
                    Hanya buku yang tersedia
                </label>
                @if (request()->query())
                    <a href="{{ route('catalog.index') }}" class="underline underline-offset-2">Hapus filter</a>
                @endif
            </div>
        </form>
    </div>
    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-10" style="background: white;"></div>
    <div class="absolute -bottom-8 right-20 w-24 h-24 rounded-full opacity-10" style="background: white;"></div>
</div>

<p class="mb-4 text-sm" style="color: #6b7280;" role="status">
    @if ($books->total() > 0)
        Menampilkan {{ $books->firstItem() }}–{{ $books->lastItem() }} dari {{ $books->total() }} buku
    @else
        Tidak ada buku yang cocok dengan pencarian atau filter.
    @endif
</p>

@if ($books->isEmpty())
    <div class="bg-white rounded-2xl border p-12 text-center shadow-sm" style="border-color: #f3f4f6;">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #7c3aed;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
        <p class="font-semibold" style="color: #374151;">Buku tidak ditemukan.</p>
        <p class="text-sm mt-1" style="color: #9ca3af;">Coba kata kunci yang berbeda</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($books as $book)
            <article class="bg-white rounded-2xl border shadow-sm card-hover overflow-hidden" style="border-color: #f3f4f6;">
                <div class="h-28 flex items-center justify-center relative" style="background: linear-gradient(135deg, #ede9fe, #ddd6fe);">
                    @if ($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Sampul {{ $book->title }}" class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <svg class="w-14 h-14 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #7c3aed;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    @endif
                    <span class="absolute top-3 right-3 z-10 badge-purple">{{ $book->category->name }}</span>
                </div>
                <div class="p-4">
                    <h2 class="font-bold text-sm leading-tight mb-1" style="color: #111827;">{{ $book->title }}</h2>
                    <p class="text-xs mb-1" style="color: #6b7280;">{{ $book->author->name }}</p>
                    <p class="text-xs mb-3" style="color: #9ca3af;">{{ $book->publisher->name }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium {{ $book->stock > 0 ? 'badge-green' : 'badge-red' }}">
                            {{ $book->stock > 0 ? 'Tersedia ('.$book->stock.')' : 'Habis' }}
                        </span>
                        <a href="{{ route('catalog.show', $book) }}" class="text-xs font-semibold flex items-center gap-1" style="color: #7c3aed;">
                            Detail
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
@endif
@endsection
