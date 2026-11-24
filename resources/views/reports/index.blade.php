@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="mb-6">
    <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Analitik</p>
    <h1 class="text-2xl font-bold" style="color: #111827;">Laporan Transaksi</h1>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border shadow-sm p-5 mb-5" style="border-color: #f3f4f6;">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-input" style="width: auto;">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-input" style="width: auto;">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Terapkan
            </button>
            <a href="{{ route('reports.index') }}" class="btn-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Summary cards -->
<div class="grid sm:grid-cols-2 gap-4 mb-5">
    <div class="bg-white rounded-2xl border shadow-sm p-5" style="border-color: #f3f4f6;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: #ede9fe;">
            <svg class="w-5 h-5" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-2xl font-bold" style="color: #111827;">{{ $fineCount }}</p>
        <p class="text-xs mt-1 font-medium" style="color: #6b7280;">Jumlah Transaksi Denda</p>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm p-5" style="border-color: #f3f4f6;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: #dcfce7;">
            <svg class="w-5 h-5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-2xl font-bold" style="color: #111827;">Rp{{ number_format($fineTotal, 0, ',', '.') }}</p>
        <p class="text-xs mt-1 font-medium" style="color: #6b7280;">Total Denda Tercatat</p>
    </div>
</div>

<!-- Loans table -->
<div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-5" style="border-color: #f3f4f6;">
    <div class="px-6 py-4 border-b" style="border-color: #f3f4f6;">
        <h2 class="font-bold" style="color: #111827;">Data Peminjaman</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Tanggal Pinjam</th>
                    <th class="table-th text-left">Anggota</th>
                    <th class="table-th text-left">Buku</th>
                    <th class="table-th text-left">Jatuh Tempo</th>
                    <th class="table-th text-left">Status</th>
                    <th class="table-th text-left">Denda</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($loans as $loan)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td">{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td class="table-td font-medium" style="color: #111827;">{{ $loan->user->name }}</td>
                        <td class="table-td max-w-xs truncate">{{ $loan->items->pluck('book.title')->join(', ') }}</td>
                        <td class="table-td">{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="table-td">
                            @if ($loan->status === 'selesai')
                                <span class="badge-green">{{ ucfirst($loan->status) }}</span>
                            @elseif ($loan->status === 'terlambat')
                                <span class="badge-red">{{ ucfirst($loan->status) }}</span>
                            @else
                                <span class="badge-purple">{{ ucfirst($loan->status) }}</span>
                            @endif
                        </td>
                        <td class="table-td font-medium">{{ $loan->fine ? 'Rp'.number_format($loan->fine->amount, 0, ',', '.') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center" style="color: #9ca3af;">Tidak ada transaksi pada rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mb-8">{{ $loans->links() }}</div>

<!-- Returns table -->
<section>
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #f3f4f6;">
        <div class="px-6 py-4 border-b" style="border-color: #f3f4f6;">
            <h2 class="font-bold" style="color: #111827;">Data Pengembalian</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="background: #fafafa;">
                        <th class="table-th text-left">Tanggal Kembali</th>
                        <th class="table-th text-left">Anggota</th>
                        <th class="table-th text-left">Buku</th>
                        <th class="table-th text-left">Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: #f3f4f6;">
                    @forelse ($returns as $item)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="table-td">{{ $item->returned_at->format('d/m/Y') }}</td>
                            <td class="table-td font-medium" style="color: #111827;">{{ $item->loan->user->name }}</td>
                            <td class="table-td">{{ $item->book->title }}</td>
                            <td class="table-td"><a class="font-semibold" style="color: #7c3aed;" href="{{ route('loans.show', $item->loan) }}">#{{ $item->loan_id }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center" style="color: #9ca3af;">Tidak ada pengembalian pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $returns->links() }}</div>
</section>
@endsection
