@extends('layouts.app')

@section('title', $account->exists ? 'Ubah Akun' : 'Tambah Akun')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold mb-4" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar akun
    </a>
    <h1 class="text-2xl font-bold" style="color: #111827;">{{ $account->exists ? 'Ubah Akun' : 'Tambah Akun' }}</h1>
</div>

<div class="max-w-2xl bg-white rounded-2xl border shadow-sm p-6" style="border-color: #f3f4f6;">
    <form method="POST" action="{{ $account->exists ? route('users.update', $account) : route('users.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        @if ($account->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Nama</label>
            <input name="name" value="{{ old('name', $account->name) }}" required class="form-input" placeholder="Nama lengkap">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Email</label>
            <input type="email" name="email" value="{{ old('email', $account->email) }}" required class="form-input" placeholder="email@contoh.com">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Peran</label>
            <select name="role" required class="form-input">
                <option value="anggota" @selected(old('role', $account->role ?? 'anggota') === 'anggota')>Anggota</option>
                <option value="pustakawan" @selected(old('role', $account->role) === 'pustakawan')>Pustakawan</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Nomor Telepon</label>
            <input name="phone" value="{{ old('phone', $account->phone) }}" class="form-input" placeholder="08xx-xxxx-xxxx">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Alamat</label>
            <textarea name="address" rows="2" class="form-input resize-none" placeholder="Alamat lengkap">{{ old('address', $account->address) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
                Kata Sandi
                @if ($account->exists)
                    <span class="font-normal text-xs" style="color: #9ca3af;">(kosongkan jika tidak diubah)</span>
                @endif
            </label>
            <input type="password" name="password" @required(! $account->exists) autocomplete="new-password" class="form-input" placeholder="••••••••">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" @required(! $account->exists) autocomplete="new-password" class="form-input" placeholder="••••••••">
        </div>

        @if ($account->member_code)
            <div class="sm:col-span-2 p-3 rounded-xl" style="background: #f5f3ff;">
                <p class="text-sm" style="color: #6b7280;">Kode anggota: <strong style="color: #7c3aed;">{{ $account->member_code }}</strong></p>
            </div>
        @endif

        <div class="sm:col-span-2 flex gap-3 pt-2">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan
            </button>
            <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
