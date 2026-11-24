@extends('layouts.app')

@section('title', 'Laporan Denda')

@section('content')
<div class="mb-6">
    <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Transaksi</p>
    <h1 class="text-2xl font-bold" style="color: #111827;">Denda</h1>
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #f3f4f6;">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Anggota</th>
                    <th class="table-th text-left">Transaksi</th>
                    <th class="table-th text-left">Hari Terlambat</th>
                    <th class="table-th text-left">Jumlah</th>
                    <th class="table-th text-left">Status</th>
                    <th class="table-th text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($fines as $fine)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td font-medium" style="color: #111827;">{{ $fine->loan->user->name }}</td>
                        <td class="table-td">
                            <a href="{{ route('loans.show', $fine->loan) }}" class="font-semibold" style="color: #7c3aed;">#{{ $fine->loan_id }}</a>
                        </td>
                        <td class="table-td">{{ $fine->days_late }} hari</td>
                        <td class="table-td font-semibold" style="color: #111827;">Rp{{ number_format($fine->amount, 0, ',', '.') }}</td>
                        <td class="table-td">
                            @if ($fine->status === 'lunas')
                                <span class="badge-green">Lunas</span>
                            @else
                                <span class="badge-red">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="table-td">
                            @if ($fine->status === 'belum')
                                <form method="POST" action="{{ route('fines.pay', $fine) }}"
                                    onsubmit="return confirm('Catat pembayaran denda?')">
                                    @csrf
                                    <button type="submit" class="text-sm font-semibold" style="color: #16a34a;">Tandai Lunas</button>
                                </form>
                            @else
                                <span class="text-xs" style="color: #9ca3af;">{{ $fine->paid_at?->format('d/m/Y H:i') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center" style="color: #9ca3af;">
                            Belum ada data denda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $fines->links() }}</div>
@endsection
