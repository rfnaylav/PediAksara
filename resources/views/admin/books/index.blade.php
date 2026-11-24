@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Data Master</p>
        <h1 class="text-2xl font-bold" style="color: #111827;">Buku</h1>
    </div>
    <a href="{{ route('books.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Buku
    </a>
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #f3f4f6;">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Judul</th>
                    <th class="table-th text-left">Kategori</th>
                    <th class="table-th text-left">Penulis</th>
                    <th class="table-th text-left">ISBN</th>
                    <th class="table-th text-left">Stok</th>
                    <th class="table-th text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($books as $book)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td font-medium" style="color: #111827;">{{ $book->title }}</td>
                        <td class="table-td"><span class="badge-purple">{{ $book->category->name }}</span></td>
                        <td class="table-td" style="color: #6b7280;">{{ $book->author->name }}</td>
                        <td class="table-td" style="color: #9ca3af;">{{ $book->isbn ?: '—' }}</td>
                        <td class="table-td">
                            <span class="{{ $book->stock > 0 ? 'badge-green' : 'badge-red' }}">{{ $book->stock }}</span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('books.edit', $book) }}" class="text-sm font-semibold" style="color: #7c3aed;">Ubah</a>
                                <form method="POST" action="{{ route('books.destroy', $book) }}"
                                    onsubmit="return confirm('Hapus buku ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold" style="color: #dc2626;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center" style="color: #9ca3af;">Belum ada buku.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $books->links() }}</div>
@endsection
