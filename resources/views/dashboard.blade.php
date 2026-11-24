@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Hero Banner -->
<div class="rounded-2xl mb-6 p-6 relative overflow-hidden flex items-center justify-between" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); min-height: 140px;">
    <div class="relative z-10">
        <p class="text-purple-200 text-sm font-medium mb-1">Selamat datang kembali 👋</p>
        <h1 class="text-2xl font-bold text-white mb-1">Dashboard Perpustakaan</h1>
        <p class="text-purple-200 text-sm">Pantau aktivitas dan statistik perpustakaan</p>
    </div>
    <div class="hidden md:block">
        <svg class="w-32 h-32 opacity-20" fill="currentColor" viewBox="0 0 24 24" style="color: white;">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
    </div>
    <!-- decorative circles -->
    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-20" style="background: white;"></div>
    <div class="absolute -bottom-8 right-20 w-24 h-24 rounded-full opacity-10" style="background: white;"></div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @foreach ([
        ['Judul Buku', $bookCount, 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', '#7c3aed', '#ede9fe'],
        ['Stok Tersedia', $availableCopies, 'M5 13l4 4L19 7', '#16a34a', '#dcfce7'],
        ['Peminjaman Aktif', $activeLoans, 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', '#d97706', '#fef9c3'],
        ['Terlambat', $overdueLoans, 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', '#dc2626', '#fee2e2'],
        ['Denda Belum Lunas', 'Rp'.number_format($unpaidFines, 0, ',', '.'), 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', '#7c3aed', '#ede9fe'],
    ] as [$label, $value, $icon, $iconColor, $iconBg])
    <div class="bg-white rounded-2xl p-5 shadow-sm card-hover border" style="border-color: #f3f4f6;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: {{ $iconBg }};">
            <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <p class="text-2xl font-bold" style="color: #111827;">{{ $value }}</p>
        <p class="text-xs mt-1 font-medium" style="color: #6b7280;">{{ $label }}</p>
    </div>
    @endforeach
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-2xl shadow-sm border" style="border-color: #f3f4f6;">
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: #f3f4f6;">
        <div>
            <h2 class="text-base font-bold" style="color: #111827;">Transaksi Terbaru</h2>
            <p class="text-xs mt-0.5" style="color: #6b7280;">Daftar peminjaman terkini</p>
        </div>
        <a href="{{ route('loans.index') }}" class="text-sm font-semibold flex items-center gap-1" style="color: #7c3aed;">
            Lihat semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Anggota</th>
                    <th class="table-th text-left">Tanggal Pinjam</th>
                    <th class="table-th text-left">Jatuh Tempo</th>
                    <th class="table-th text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($recentLoans as $loan)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td font-medium" style="color: #111827;">{{ $loan->user->name }}</td>
                        <td class="table-td" style="color: #6b7280;">{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td class="table-td" style="color: #6b7280;">{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="table-td">
                            @if ($loan->status === 'selesai')
                                <span class="badge-green">{{ ucfirst($loan->status) }}</span>
                            @elseif ($loan->status === 'terlambat')
                                <span class="badge-red">{{ ucfirst($loan->status) }}</span>
                            @else
                                <span class="badge-purple">{{ ucfirst($loan->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center" style="color: #9ca3af;">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Belum ada transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
