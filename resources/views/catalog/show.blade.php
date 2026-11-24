@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="mb-5">
    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke katalog
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-5 max-w-5xl">
    <!-- Book visual -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl flex items-center justify-center h-64 lg:h-80 overflow-hidden" style="background: linear-gradient(135deg, #ede9fe, #ddd6fe);">
            @if ($book->cover_image)
                <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Sampul {{ $book->title }}" class="h-full w-full object-cover">
            @else
            <svg class="w-24 h-24 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #7c3aed;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            @endif
        </div>
    </div>

    <!-- Book detail -->
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl border shadow-sm p-6" style="border-color: #f3f4f6;">
            <span class="badge-purple mb-3 inline-block">{{ $book->category->name }}</span>
            <h1 class="text-2xl font-bold mb-2" style="color: #111827;">{{ $book->title }}</h1>

            <dl class="grid sm:grid-cols-2 gap-4 mt-5">
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <dt class="text-xs mb-1" style="color: #9ca3af;">Penulis</dt>
                    <dd class="font-semibold text-sm" style="color: #111827;">{{ $book->author->name }}</dd>
                </div>
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <dt class="text-xs mb-1" style="color: #9ca3af;">Penerbit</dt>
                    <dd class="font-semibold text-sm" style="color: #111827;">{{ $book->publisher->name }}</dd>
                </div>
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <dt class="text-xs mb-1" style="color: #9ca3af;">ISBN</dt>
                    <dd class="font-semibold text-sm" style="color: #111827;">{{ $book->isbn ?: '—' }}</dd>
                </div>
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <dt class="text-xs mb-1" style="color: #9ca3af;">Tahun Terbit</dt>
                    <dd class="font-semibold text-sm" style="color: #111827;">{{ $book->year ?: '—' }}</dd>
                </div>
                <div class="rounded-xl p-4 sm:col-span-2" style="background: #f9fafb;">
                    <dt class="text-xs mb-1" style="color: #9ca3af;">Stok Tersedia</dt>
                    <dd class="font-semibold text-sm flex items-center gap-2">
                        <span class="{{ $book->stock > 0 ? 'badge-green' : 'badge-red' }}">{{ $book->stock }} eksemplar</span>
                    </dd>
                </div>
            </dl>
        </div>

        @if ($book->stock > 0)
            <div class="rounded-2xl flex items-center gap-4 p-5 shadow-sm" style="background: #dcfce7; border: 1px solid #bbf7d0;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #16a34a;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-sm" style="color: #14532d;">Buku Tersedia</p>
                    <p class="text-sm mt-0.5" style="color: #16a34a;">Silakan hubungi pustakawan untuk melakukan peminjaman.</p>
                </div>
            </div>
        @else
            <div class="rounded-2xl flex items-center gap-4 p-5 shadow-sm" style="background: #fff7ed; border: 1px solid #fed7aa;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #d97706;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-sm" style="color: #78350f;">Stok Habis</p>
                    <p class="text-sm mt-0.5" style="color: #92400e;">Stok buku saat ini sedang habis. Silakan cek kembali nanti.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
