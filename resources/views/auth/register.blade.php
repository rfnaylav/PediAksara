@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
<!-- Left side: decorative -->
<div class="hidden lg:flex flex-col justify-center items-center w-5/12 p-12 relative overflow-hidden" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
    <div class="relative z-10 text-center">
        <div class="w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-3">Bergabung Sekarang</h1>
        <p class="text-purple-200 text-sm max-w-xs mx-auto">Daftarkan diri Anda dan nikmati kemudahan layanan perpustakaan digital.</p>
        <div class="mt-8 p-5 rounded-2xl text-left" style="background: rgba(255,255,255,0.1);">
            <p class="text-white font-semibold mb-3 text-sm">Keuntungan menjadi anggota:</p>
            <ul class="space-y-2">
                @foreach (['Akses katalog lengkap', 'Lacak riwayat peminjaman', 'Notifikasi jatuh tempo', 'Perpanjang masa pinjam'] as $item)
                <li class="flex items-center gap-2 text-sm text-purple-100">
                    <svg class="w-4 h-4 text-green-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full opacity-10" style="background: white;"></div>
    <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-full opacity-10" style="background: white;"></div>
</div>

<!-- Right side: form -->
<div class="flex-1 flex items-center justify-center p-6 lg:p-12 overflow-y-auto" style="background: #f5f3ff;">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-sm border p-8" style="border-color: #ede9fe;">
            <div class="mb-6">
                <h1 class="text-2xl font-bold" style="color: #111827;">Registrasi Anggota</h1>
                <p class="mt-1 text-sm" style="color: #6b7280;">Buat akun untuk melihat katalog dan riwayat peminjaman.</p>
            </div>
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Nama Lengkap</label>
                    <input name="name" value="{{ old('name') }}" required class="form-input" placeholder="Nama lengkap Anda">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-input" placeholder="email@contoh.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Nomor Telepon</label>
                    <input name="phone" value="{{ old('phone') }}" class="form-input" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Alamat</label>
                    <textarea name="address" rows="2" class="form-input resize-none" placeholder="Alamat lengkap Anda">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Kata Sandi</label>
                    <input type="password" name="password" required autocomplete="new-password" class="form-input" placeholder="Min. 8 karakter">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-input" placeholder="Ulangi kata sandi">
                </div>
                <button type="submit" class="btn-primary w-full justify-center py-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Buat Akun
                </button>
            </form>
            <p class="mt-5 text-center text-sm" style="color: #6b7280;">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold" style="color: #7c3aed;">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
