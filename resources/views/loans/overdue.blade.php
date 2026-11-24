@extends('layouts.app')

@section('title', 'Peminjaman Terlambat')

@section('content')
<div class="mb-6">
    <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Pemantauan</p>
    <h1 class="text-2xl font-bold" style="color: #111827;">Peminjaman Terlambat</h1>
</div>

<div class="space-y-3">
    @forelse ($loans as $loan)
        <article class="bg-white rounded-2xl border shadow-sm card-hover" style="border-color: #fee2e2;">
            <div class="p-5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #fee2e2;">
                        <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold" style="color: #111827;">
                            {{ auth()->user()->hasRole('anggota') ? 'Peminjaman Saya' : $loan->user->name }}
                        </h2>
                        <p class="text-sm mt-0.5" style="color: #6b7280;">
                            Jatuh tempo: {{ $loan->due_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs mt-1 truncate max-w-sm" style="color: #9ca3af;">
                            {{ $loan->items->whereNull('returned_at')->pluck('book.title')->join(', ') }}
                        </p>
                    </div>
                </div>
                <a href="{{ auth()->user()->hasRole('anggota') ? route('member.loans.show', $loan) : route('loans.show', $loan) }}"
                    class="text-sm font-semibold flex items-center gap-1 flex-shrink-0" style="color: #7c3aed;">
                    Lihat transaksi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl border p-12 text-center shadow-sm" style="border-color: #f3f4f6;">
            <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="font-semibold" style="color: #374151;">Tidak ada peminjaman terlambat. 🎉</p>
        </div>
    @endforelse
</div>
<div class="mt-5">{{ $loans->links() }}</div>
@endsection
