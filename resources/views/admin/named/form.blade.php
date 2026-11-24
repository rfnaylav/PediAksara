@extends('layouts.app')

@section('title', ($record->exists ? 'Ubah ' : 'Tambah ').$label)

@section('content')
<div class="mb-6">
    <a href="{{ route($resource.'.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold mb-4" style="color: #7c3aed;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar
    </a>
    <h1 class="text-2xl font-bold" style="color: #111827;">{{ $record->exists ? 'Ubah ' : 'Tambah ' }}{{ strtolower($label) }}</h1>
</div>

<div class="max-w-lg bg-white rounded-2xl border shadow-sm p-6" style="border-color: #f3f4f6;">
    <form method="POST" action="{{ $record->exists ? route($resource.'.update', $record) : route($resource.'.store') }}" class="space-y-4">
        @csrf
        @if ($record->exists) @method('PUT') @endif
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">Nama</label>
            <input name="name" value="{{ old('name', $record->name) }}" required maxlength="255" class="form-input" placeholder="Nama {{ strtolower($label) }}">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan
            </button>
            <a href="{{ route($resource.'.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
