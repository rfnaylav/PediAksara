@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="mb-5">
    <a href="{{ auth()->user()->hasRole('anggota') ? route('member.loans.index') : route('loans.index') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-5">
    <!-- Main card -->
    <div class="lg:col-span-2 space-y-5">
        <section class="bg-white rounded-2xl border shadow-sm" style="border-color: #f3f4f6;">
            <div class="p-6 border-b" style="border-color: #f3f4f6;">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs mb-1" style="color: #9ca3af;">Transaksi #{{ $loan->id }}</p>
                        <h1 class="text-xl font-bold" style="color: #111827;">Peminjaman {{ $loan->user->name }}</h1>
                    </div>
                    @if ($loan->status === 'selesai')
                        <span class="badge-green">{{ ucfirst($loan->status) }}</span>
                    @elseif ($loan->status === 'terlambat')
                        <span class="badge-red">{{ ucfirst($loan->status) }}</span>
                    @else
                        <span class="badge-purple">{{ ucfirst($loan->status) }}</span>
                    @endif
                </div>
            </div>
            <div class="p-6 grid sm:grid-cols-3 gap-4">
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <p class="text-xs mb-1" style="color: #9ca3af;">Tanggal Pinjam</p>
                    <p class="font-semibold text-sm" style="color: #111827;">{{ $loan->loan_date->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <p class="text-xs mb-1" style="color: #9ca3af;">Jatuh Tempo</p>
                    <p class="font-semibold text-sm" style="color: #111827;">{{ $loan->due_date->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-xl p-4" style="background: #f9fafb;">
                    <p class="text-xs mb-1" style="color: #9ca3af;">Perpanjangan</p>
                    <p class="font-semibold text-sm" style="color: #111827;">{{ $loan->extend_count }} / {{ \App\Models\Loan::MAX_EXTENSIONS }}x</p>
                </div>
            </div>
        </section>

        <!-- Book List -->
        <section class="bg-white rounded-2xl border shadow-sm" style="border-color: #f3f4f6;">
            <div class="px-6 py-4 border-b" style="border-color: #f3f4f6;">
                <h2 class="font-bold" style="color: #111827;">Daftar Buku</h2>
            </div>
            <div class="divide-y" style="border-color: #f3f4f6;">
                @foreach ($loan->items as $item)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ede9fe;">
                                <svg class="w-5 h-5" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-sm" style="color: #111827;">{{ $item->book->title }}</p>
                                <p class="text-xs mt-0.5" style="color: {{ $item->returned_at ? '#16a34a' : '#9ca3af' }};">
                                    {{ $item->returned_at ? '✓ Dikembalikan '.$item->returned_at->format('d/m/Y') : 'Belum dikembalikan' }}
                                </p>
                            </div>
                        </div>
                        @if (auth()->user()->hasRole('pustakawan') && ! $item->returned_at)
                            <form method="POST" action="{{ route('loans.items.return', [$loan, $item]) }}"
                                onsubmit="return confirm('Catat pengembalian buku ini?')">
                                @csrf
                                <button type="submit" class="btn-green text-xs py-2 px-3">Catat Pengembalian</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        @if (auth()->user()->hasRole('pustakawan') && $loan->status === 'dipinjam' && $loan->extend_count < \App\Models\Loan::MAX_EXTENSIONS)
            <form method="POST" action="{{ route('loans.extend', $loan) }}">
                @csrf
                <button type="submit" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Perpanjang 7 Hari
                </button>
            </form>
        @endif
    </div>

    <!-- Fine Card -->
    <div class="space-y-5">
        @if ($loan->fine)
            <div class="rounded-2xl p-5 border shadow-sm {{ $loan->fine->status === 'lunas' ? '' : '' }}" style="background: {{ $loan->fine->status === 'lunas' ? '#dcfce7' : '#fff7ed' }}; border-color: {{ $loan->fine->status === 'lunas' ? '#bbf7d0' : '#fed7aa' }};">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $loan->fine->status === 'lunas' ? '#16a34a' : '#d97706' }};">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide" style="color: {{ $loan->fine->status === 'lunas' ? '#15803d' : '#92400e' }};">Informasi Denda</p>
                        <p class="font-bold text-lg" style="color: {{ $loan->fine->status === 'lunas' ? '#14532d' : '#78350f' }};">Rp{{ number_format($loan->fine->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                <p class="text-sm" style="color: {{ $loan->fine->status === 'lunas' ? '#16a34a' : '#92400e' }};">{{ $loan->fine->days_late }} hari terlambat</p>
                <span class="{{ $loan->fine->status === 'lunas' ? 'badge-green' : 'badge-yellow' }} mt-2 inline-block">{{ ucfirst($loan->fine->status) }}</span>
            </div>
        @else
            <div class="bg-white rounded-2xl border p-5 shadow-sm text-center" style="border-color: #f3f4f6;">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #7c3aed;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2"/></svg>
                <p class="text-sm font-medium" style="color: #9ca3af;">Tidak ada denda</p>
            </div>
        @endif
    </div>
</div>
@endsection
