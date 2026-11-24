@extends('layouts.app')

@section('title', $title)

@section('content')
<!-- Page header -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Transaksi</p>
        <h1 class="text-2xl font-bold" style="color: #111827;">{{ $title }}</h1>
    </div>
    @unless ($memberView)
        <a href="{{ route('loans.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Peminjaman
        </a>
    @endunless
</div>

<div class="space-y-3">
    @forelse ($loans as $loan)
        <article class="bg-white rounded-2xl border shadow-sm card-hover" style="border-color: #f3f4f6;">
            <div class="p-5">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-xs mb-1" style="color: #9ca3af;">Peminjaman #{{ $loan->id }} · {{ $loan->loan_date->format('d/m/Y') }}</p>
                        <h2 class="font-bold" style="color: #111827;">{{ $memberView ? 'Peminjaman Saya' : $loan->user->name }}</h2>
                    </div>
                    @if ($loan->status === 'selesai')
                        <span class="badge-green">{{ ucfirst($loan->status) }}</span>
                    @elseif ($loan->status === 'terlambat')
                        <span class="badge-red">{{ ucfirst($loan->status) }}</span>
                    @else
                        <span class="badge-purple">{{ ucfirst($loan->status) }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-4 text-sm mb-3" style="color: #6b7280;">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Jatuh tempo {{ $loan->due_date->format('d/m/Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5"/></svg>
                        {{ $loan->items->count() }} buku
                    </span>
                </div>

                <p class="text-sm mb-3" style="color: #374151;">{{ $loan->items->pluck('book.title')->join(', ') }}</p>

                @if ($loan->fine)
                    <p class="text-sm font-semibold {{ $loan->fine->status === 'lunas' ? 'text-green-600' : 'text-red-600' }}">
                        Denda Rp{{ number_format($loan->fine->amount, 0, ',', '.') }} · {{ $loan->fine->status }}
                    </p>
                @endif

                <div class="mt-3 pt-3 border-t" style="border-color: #f3f4f6;">
                    <a href="{{ $memberView ? route('member.loans.show', $loan) : route('loans.show', $loan) }}"
                        class="text-sm font-semibold flex items-center gap-1" style="color: #7c3aed;">
                        Detail transaksi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl border p-12 text-center shadow-sm" style="border-color: #f3f4f6;">
            <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #7c3aed;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="font-semibold" style="color: #374151;">Belum ada peminjaman.</p>
        </div>
    @endforelse
</div>
<div class="mt-5">{{ $loans->links() }}</div>
@endsection
