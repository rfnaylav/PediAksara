@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: #8b5cf6;">Pengguna</p>
        <h1 class="text-2xl font-bold" style="color: #111827;">Akun Anggota & Pustakawan</h1>
    </div>
    <a href="{{ route('users.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Akun
    </a>
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #f3f4f6;">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: #fafafa;">
                    <th class="table-th text-left">Nama</th>
                    <th class="table-th text-left">Email</th>
                    <th class="table-th text-left">Peran</th>
                    <th class="table-th text-left">Kode Anggota</th>
                    <th class="table-th text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #f3f4f6;">
                @forelse ($users as $account)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs text-white flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                                    {{ strtoupper(substr($account->name, 0, 1)) }}
                                </div>
                                <span class="font-medium" style="color: #111827;">{{ $account->name }}</span>
                            </div>
                        </td>
                        <td class="table-td" style="color: #6b7280;">{{ $account->email }}</td>
                        <td class="table-td">
                            @if ($account->role === 'admin')
                                <span class="badge-purple">Admin</span>
                            @elseif ($account->role === 'pustakawan')
                                <span class="badge-yellow">Pustakawan</span>
                            @else
                                <span class="badge-green">Anggota</span>
                            @endif
                        </td>
                        <td class="table-td" style="color: #9ca3af;">{{ $account->member_code ?: '—' }}</td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('users.edit', $account) }}" class="text-sm font-semibold" style="color: #7c3aed;">Ubah</a>
                                <form method="POST" action="{{ route('users.destroy', $account) }}"
                                    onsubmit="return confirm('Hapus akun ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold" style="color: #dc2626;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center" style="color: #9ca3af;">Belum ada akun.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $users->links() }}</div>
@endsection
