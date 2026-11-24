<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Perpustakaan') · {{ config('app.name', 'Perpustakaan') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link.active { background: #7c3aed; color: white; }
        .sidebar-link:not(.active):hover { background: #f3f0ff; color: #7c3aed; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(124,58,237,0.15); }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #7c3aed; }
        .btn-primary { background: #7c3aed; color: white; padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: background 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-primary:hover { background: #6d28d9; }
        .btn-secondary { background: white; color: #374151; padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; border: 1px solid #e5e7eb; transition: all 0.2s; }
        .btn-secondary:hover { border-color: #7c3aed; color: #7c3aed; }
        .btn-green { background: #16a34a; color: white; padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: background 0.2s; }
        .btn-green:hover { background: #15803d; }
        .form-input { width: 100%; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.6rem 0.9rem; font-size: 0.875rem; color: #111827; background: white; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
        .table-th { padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
        .table-td { padding: 0.875rem 1rem; font-size: 0.875rem; color: #374151; }
        .badge-purple { background: #ede9fe; color: #6d28d9; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #15803d; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-red { background: #fee2e2; color: #b91c1c; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-yellow { background: #fef9c3; color: #a16207; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .sidebar-mobile-overlay { display: none; }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); transition: transform 0.3s; z-index: 50; }
            #sidebar.open { transform: translateX(0); }
            .sidebar-mobile-overlay { display: block; }
        }
    </style>
</head>
<body class="min-h-screen" style="background: #f5f3ff;">

@auth
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 w-64 flex flex-col" style="background: white; border-right: 1px solid #ede9fe; min-height: 100vh;">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b" style="border-color: #ede9fe;">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: #7c3aed;">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm" style="color: #1e1b4b;">Perpustakaan</p>
                <p class="text-xs" style="color: #8b5cf6;">Sistem Manajemen</p>
            </div>
        </div>

        <!-- Nav Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
            @auth
                <p class="text-xs font-semibold uppercase tracking-widest px-3 mb-3" style="color: #9ca3af;">Menu Utama</p>

                <a href="{{ route('catalog.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('catalog.*') ? 'active' : 'text-gray-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Katalog Buku
                </a>

                @if (auth()->user()->hasRole('admin', 'pustakawan'))
                    <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    <p class="text-xs font-semibold uppercase tracking-widest px-3 mb-1 mt-4" style="color: #9ca3af;">Transaksi</p>

                    <a href="{{ route('loans.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('loans.index') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Peminjaman
                    </a>

                    <a href="{{ route('loans.overdue') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('loans.overdue') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terlambat
                    </a>

                    <a href="{{ route('fines.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('fines.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Denda
                    </a>

                    <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('reports.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan
                    </a>
                @endif

                @if (auth()->user()->hasRole('admin'))
                    <p class="text-xs font-semibold uppercase tracking-widest px-3 mb-1 mt-4" style="color: #9ca3af;">Admin</p>

                    <a href="{{ route('books.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('books.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Kelola Buku
                    </a>

                    <a href="{{ route('categories.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('categories.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/></svg>
                        Kategori
                    </a>

                    <a href="{{ route('authors.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('authors.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Penulis
                    </a>

                    <a href="{{ route('publishers.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('publishers.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Penerbit
                    </a>

                    <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('users.*') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola Akun
                    </a>
                @endif

                @if (auth()->user()->hasRole('anggota'))
                    <p class="text-xs font-semibold uppercase tracking-widest px-3 mb-1 mt-4" style="color: #9ca3af;">Riwayat</p>
                    <a href="{{ route('member.loans.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('member.loans.index') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Riwayat Saya
                    </a>
                    <a href="{{ route('member.loans.overdue') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('member.loans.overdue') ? 'active' : 'text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Keterlambatan
                    </a>
                @endif
            @endauth
        </nav>

        <!-- Bottom user info -->
        <div class="px-4 py-4 border-t" style="border-color: #ede9fe;">
            <div class="flex items-center gap-3 p-2 rounded-xl" style="background: #f5f3ff;">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm text-white flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: #1e1b4b;">{{ auth()->user()->name ?? '' }}</p>
                    <p class="text-xs truncate" style="color: #8b5cf6;">{{ ucfirst(auth()->user()->role ?? '') }}</p>
                </div>
            </div>
            <a href="{{ route('account.password.edit') }}" class="sidebar-link mt-2 w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600">
                Ubah kata sandi
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top navbar -->
        <header class="flex items-center justify-between px-6 py-4 bg-white border-b" style="border-color: #ede9fe;">
            <!-- Mobile hamburger -->
            <button onclick="document.getElementById('sidebar').classList.toggle('open')" class="lg:hidden p-2 rounded-lg hover:bg-purple-50">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex items-center gap-3">
                <h2 class="text-base font-semibold hidden sm:block" style="color: #1e1b4b;">@yield('title', 'Halaman')</h2>
            </div>

            <div class="flex items-center gap-3 ml-auto">
                <!-- Search -->
                <form method="GET" action="{{ route('catalog.index') }}" class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl" style="background: #f5f3ff;">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input name="q" placeholder="Cari buku..." class="bg-transparent text-sm outline-none w-40" style="color: #374151;">
                </form>

                <!-- Bell -->
                <button class="relative p-2 rounded-xl" style="background: #f5f3ff;">
                    <svg class="w-5 h-5" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>

                <!-- User chip -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl" style="background: #f5f3ff;">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium hidden sm:block" style="color: #1e1b4b;">{{ auth()->user()->name ?? '' }}</span>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @if (session('status'))
                <div class="mb-5 flex items-center gap-3 rounded-xl px-4 py-3" style="background: #dcfce7; border: 1px solid #bbf7d0;" role="status">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-medium" style="color: #15803d;">{{ session('status') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-xl px-4 py-3" style="background: #fee2e2; border: 1px solid #fecaca;" role="alert">
                    <p class="font-semibold text-sm" style="color: #b91c1c;">Periksa kembali data yang dimasukkan.</p>
                    <ul class="mt-1 list-inside list-disc text-sm" style="color: #b91c1c;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@else
<!-- Guest layout -->
<div class="min-h-screen flex" style="background: #f5f3ff;">
    @yield('content')
</div>
@endauth

<script>
    // Close sidebar on overlay click (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !e.target.closest('[onclick]')) {
                sidebar.classList.remove('open');
            }
        }
    });
</script>

</body>
</html>
