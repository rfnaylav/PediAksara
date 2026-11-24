@extends('layouts.app')

@section('title', $label)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Data Master</p>
        <h1 class="text-2xl font-bold" style="color: #111827;">{{ $label }}</h1>
    </div>
    <a href="{{ route($resource.'.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah {{ strtolower($label) }}
    </a>
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #f3f4f6;">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Nama</th>
                    <th class="table-th text-left">Jumlah Buku</th>
                    <th class="table-th text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($records as $record)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td font-medium" style="color: #111827;">{{ $record->name }}</td>
                        <td class="table-td"><span class="badge-purple">{{ $record->books_count }}</span></td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route($resource.'.edit', $record) }}" class="text-sm font-semibold" style="color: #7c3aed;">Ubah</a>
                                <form method="POST" action="{{ route($resource.'.destroy', $record) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold" style="color: #dc2626;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-10 text-center" style="color: #9ca3af;">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $records->links() }}</div>
@endsection
