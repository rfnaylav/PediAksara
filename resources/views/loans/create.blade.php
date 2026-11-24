@extends('layouts.app')

@section('title', 'Catat Peminjaman')

@section('content')
<div class="mb-6">
    <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold mb-4" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="text-2xl font-bold" style="color: #111827;">Catat Peminjaman</h1>
    <p class="text-sm mt-1" style="color: #6b7280;">Maksimal {{ \App\Models\Loan::MAX_ACTIVE_BOOKS }} buku aktif per anggota. Jatuh tempo otomatis {{ \App\Models\Loan::LOAN_DAYS }} hari dari tanggal pinjam.</p>
</div>

<div class="max-w-2xl bg-white rounded-2xl border shadow-sm p-6" style="border-color: #f3f4f6;">
    <form method="POST" action="{{ route('loans.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Anggota</label>
            <select name="user_id" required class="form-input">
                <option value="">— Pilih anggota —</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}" @selected(old('user_id') == $member->id)>
                        {{ $member->name }} ({{ $member->member_code }})
                    </option>
                @endforeach
            </select>
        </div>

        <fieldset>
            <legend class="text-sm font-medium mb-2" style="color: #374151;">Buku (pilih maksimal {{ \App\Models\Loan::MAX_ACTIVE_BOOKS }})</legend>
            <div class="max-h-72 overflow-auto rounded-xl border p-3 space-y-1" style="border-color: #e5e7eb;">
                @forelse ($books as $book)
                    <label class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 cursor-pointer hover:bg-purple-50 transition-colors">
                        <span class="flex items-center gap-3">
                            <input type="checkbox" name="book_ids[]" value="{{ $book->id }}"
                                @checked(in_array($book->id, old('book_ids', [])))
                                class="rounded accent-purple-600">
                            <span class="text-sm font-medium" style="color: #374151;">{{ $book->title }}</span>
                        </span>
                        <span class="text-xs flex-shrink-0 badge-green">Stok {{ $book->stock }}</span>
                    </label>
                @empty
                    <p class="text-sm py-4 text-center" style="color: #9ca3af;">Tidak ada buku tersedia untuk dipinjam.</p>
                @endforelse
            </div>
        </fieldset>

        <div class="flex gap-3 pt-2">
            <button @disabled($members->isEmpty() || $books->isEmpty()) type="submit" class="btn-primary disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Peminjaman
            </button>
            <a href="{{ route('loans.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
