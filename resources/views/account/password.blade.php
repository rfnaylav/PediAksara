@extends('layouts.app')

@section('title', 'Ubah kata sandi')

@section('content')
    <section class="max-w-xl rounded-2xl bg-white p-6 shadow-sm" style="border: 1px solid #ede9fe;">
        <h1 class="text-2xl font-bold" style="color: #1e1b4b;">Ubah kata sandi</h1>
        <p class="mt-2 text-sm text-gray-500">Masukkan kata sandi saat ini dan pilih kata sandi baru.</p>
        <form method="POST" action="{{ route('account.password.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <label class="block text-sm font-medium text-gray-700">Kata sandi saat ini
                <input type="password" name="current_password" required autocomplete="current-password" class="form-input mt-1">
            </label>
            <label class="block text-sm font-medium text-gray-700">Kata sandi baru
                <input type="password" name="password" required autocomplete="new-password" class="form-input mt-1">
            </label>
            <label class="block text-sm font-medium text-gray-700">Konfirmasi kata sandi baru
                <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-input mt-1">
            </label>
            <button type="submit" class="btn-primary">Simpan kata sandi</button>
        </form>
    </section>
@endsection
