@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<!-- Left side: decorative -->
<div class="hidden lg:flex flex-col justify-center items-center w-1/2 p-12 relative overflow-hidden" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
    <div class="relative z-10 text-center">
        <div class="w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-3">Perpustakaan</h1>
        <p class="text-purple-200 text-base max-w-xs mx-auto">Sistem manajemen perpustakaan modern yang mudah digunakan</p>

        <div class="mt-10 grid grid-cols-3 gap-4 max-w-xs mx-auto">
            <div class="rounded-xl p-4 text-center" style="background: rgba(255,255,255,0.1);">
                <p class="text-2xl font-bold text-white">500+</p>
                <p class="text-purple-200 text-xs mt-1">Koleksi</p>
            </div>
            <div class="rounded-xl p-4 text-center" style="background: rgba(255,255,255,0.1);">
                <p class="text-2xl font-bold text-white">200+</p>
                <p class="text-purple-200 text-xs mt-1">Anggota</p>
            </div>
            <div class="rounded-xl p-4 text-center" style="background: rgba(255,255,255,0.1);">
                <p class="text-2xl font-bold text-white">1K+</p>
                <p class="text-purple-200 text-xs mt-1">Transaksi</p>
            </div>
        </div>
    </div>
    <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full opacity-10" style="background: white;"></div>
    <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-full opacity-10" style="background: white;"></div>
</div>

<!-- Right side: form -->
<div class="flex-1 flex items-center justify-center p-6 lg:p-12" style="background: #f5f3ff;">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-sm border p-8" style="border-color: #ede9fe;">
            <div class="mb-6">
                <div class="w-12 h-12 rounded-xl mb-4 flex items-center justify-center" style="background: #ede9fe;">
                    <svg class="w-6 h-6" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h1 class="text-2xl font-bold" style="color: #111827;">Selamat Datang!</h1>
                <p class="mt-1 text-sm" style="color: #6b7280;">Masuk untuk mengakses layanan perpustakaan.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        class="form-input" placeholder="email@contoh.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Kata Sandi</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="form-input" placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm cursor-pointer" style="color: #6b7280;">
                        <input type="checkbox" name="remember" value="1" class="rounded accent-purple-600">
                        Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn-primary w-full justify-center py-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk
                </button>
            </form>
            <p class="mt-5 text-center text-sm" style="color: #6b7280;">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold" style="color: #7c3aed;">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
