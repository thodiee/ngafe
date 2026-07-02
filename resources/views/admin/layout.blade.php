<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ngafe Management</title>
    <!-- SPA Navigation for instant page load -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
    <!-- Chart.js loaded globally in head for Turbo SPA compatibility -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Anti-Flicker Script for Theme State -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fcfaf2',
                            100: '#f7f0df',
                            200: '#eee0bf',
                            300: '#e2cb94',
                            400: '#dfc067',
                            500: '#d4af37',
                            600: '#b08e26',
                            700: '#8c6f1a',
                            900: '#47360a',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #94a3b8; border-radius: 20px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; }

        /* Collapsed Sidebar Styles */
        #sidebar.collapsed { width: 5rem; }
        #sidebar.collapsed .sidebar-text { display: none; }
        #sidebar.collapsed .fa-chevron-right { display: none; }
        #sidebar.collapsed [id^="submenu-"] { display: none !important; }
        #sidebar.collapsed a, #sidebar.collapsed button { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed i.w-6 { margin-right: 0; }
        #sidebar.collapsed .section-label { display: none; }
        #sidebar.collapsed .section-icon { font-size: 1.25rem; }
        #sidebar.collapsed .section-btn { padding: 0.75rem 0; justify-content: center; }
        #mainContent.collapsed-main { margin-left: 5rem; }

        /* Sidebar Tooltip on Collapsed */
        .sidebar-tooltip { display: none; }
        #sidebar.collapsed .sidebar-tooltip {
            display: none;
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            z-index: 100;
            pointer-events: none;
            background: #1e293b;
            color: #f8fafc;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .dark #sidebar.collapsed .sidebar-tooltip {
            background: #0f172a;
            border: 1px solid #334155;
        }
        #sidebar.collapsed a:hover .sidebar-tooltip,
        #sidebar.collapsed button:hover .sidebar-tooltip {
            display: block;
        }

        /* Section icon backgrounds */
        .section-icon-wrapper {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.875rem;
            transition: transform 0.15s;
        }
        #sidebar.collapsed .section-icon-wrapper {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1.125rem;
            border-radius: 0.75rem;
        }
        #sidebar.collapsed .section-icon-wrapper:hover {
            transform: scale(1.1);
        }

        /* ============================================================ */
        /* Light Mode Overrides for Admin Child Templates               */
        /* These override hardcoded Tailwind dark-mode utilities         */
        /* so 40+ admin child templates render properly in light mode    */
        /* without requiring individual file edits.                      */
        /* ============================================================ */

        /* Card backgrounds: bg-slate-800 → light white */
        html:not(.dark) #mainContent .bg-slate-800 {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0;
        }
        html:not(.dark) #mainContent .bg-slate-800\/50 {
            background-color: rgba(248, 250, 252, 0.5) !important;
        }

        /* Section headers: bg-slate-900 → light gray */
        html:not(.dark) #mainContent .bg-slate-900 {
            background-color: #f8fafc !important;
        }
        html:not(.dark) #mainContent .bg-slate-900\/50 {
            background-color: rgba(248, 250, 252, 0.5) !important;
        }

        /* Text colors */
        html:not(.dark) #mainContent .text-white {
            color: #2c2520 !important;
        }
        html:not(.dark) #mainContent .text-slate-300 {
            color: #475569 !important;
        }
        html:not(.dark) #mainContent .text-slate-400 {
            color: #64748b !important;
        }
        html:not(.dark) #mainContent .text-slate-200 {
            color: #334155 !important;
        }

        /* Border colors */
        html:not(.dark) #mainContent .border-slate-700 {
            border-color: #e2e8f0 !important;
        }
        html:not(.dark) #mainContent .border-slate-600 {
            border-color: #cbd5e1 !important;
        }

        /* Hover states */
        html:not(.dark) #mainContent .hover\:bg-slate-700:hover {
            background-color: #f1f5f9 !important;
        }

        /* Form inputs */
        html:not(.dark) #mainContent input,
        html:not(.dark) #mainContent select,
        html:not(.dark) #mainContent textarea {
            background-color: #ffffff !important;
            color: #2c2520 !important;
            border-color: #e2e8f0 !important;
        }
        html:not(.dark) #mainContent input:focus,
        html:not(.dark) #mainContent select:focus,
        html:not(.dark) #mainContent textarea:focus {
            border-color: #d4af37 !important;
        }

        /* Table rows */
        html:not(.dark) #mainContent .border-slate-800\/50 {
            border-color: #f1f5f9 !important;
        }
        html:not(.dark) #mainContent .hover\:bg-slate-800\/30:hover {
            background-color: #f8fafc !important;
        }

        /* Modal backgrounds */
        html:not(.dark) .bg-slate-800 {
            /* Modals outside mainContent also need override */
        }

        /* Badge backgrounds */
        html:not(.dark) #mainContent .bg-slate-700 {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
        }
    </style>

    <!-- Theme Toggle Scripts -->
    <script>
        function updateAdminThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const sunIcon = document.getElementById('adminSunIcon');
            const moonIcon = document.getElementById('adminMoonIcon');
            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                } else {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                }
            }
        }
        function toggleAdminTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateAdminThemeIcon();
        }
        document.addEventListener('DOMContentLoaded', () => {
            updateAdminThemeIcon();
        });
        document.addEventListener('turbo:load', () => {
            updateAdminThemeIcon();
        });
    </script>
</head>
<body class="bg-[#faf9f6] text-[#2c2520] dark:bg-slate-900 dark:text-slate-200 antialiased min-h-screen flex transition-colors duration-200">
    
    <!-- Universal Header -->
    <div class="fixed top-0 left-0 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 h-16 z-50 flex items-center justify-between px-4 transition-all duration-300">
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="text-slate-500 dark:text-slate-300 hover:text-[#2c2520] dark:hover:text-white focus:outline-none mr-4">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
            <a href="/" class="text-xl font-bold text-[#2c2520] dark:text-white flex items-center"><i class="fa-solid fa-mug-hot text-brand-500 mr-2"></i> <span class="sidebar-text">Ngafe Admin</span></a>
        </div>
        <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button onclick="toggleAdminTheme()" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-brand-500 hover:text-brand-600 transition" title="Ganti Tema">
                <i class="fa-solid fa-sun text-amber-500" id="adminSunIcon"></i>
                <i class="fa-solid fa-moon text-brand-400 hidden" id="adminMoonIcon"></i>
            </button>
            <div class="text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-user-circle text-brand-500 mr-1"></i> {{ auth()->user()->name }}
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col fixed h-full z-40 transition-all duration-300 -translate-x-full md:translate-x-0 pt-16">
        <nav class="flex-grow px-4 space-y-2 overflow-y-auto pt-4 pb-4 custom-scrollbar">
            @if(in_array(auth()->user()->role, ['admin', 'owner']))
            <a href="{{ route('admin.dashboard') }}" class="relative flex items-center px-4 py-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }}">
                <div class="section-icon-wrapper bg-blue-500/15 text-blue-500"><i class="fa-solid fa-chart-line"></i></div>
                <span class="sidebar-text ml-3">Dashboard</span>
                <span class="sidebar-tooltip">Dashboard</span>
            </a>
            <a href="{{ route('admin.owner.report') }}" class="relative flex items-center px-4 py-3 rounded-lg hover:bg-amber-500/20 hover:text-amber-400 transition {{ request()->routeIs('admin.owner.report') ? 'bg-amber-500/20 text-amber-400' : 'text-slate-600 dark:text-slate-300' }}">
                <div class="section-icon-wrapper bg-amber-500/15 text-amber-500"><i class="fa-solid fa-crown"></i></div>
                <span class="sidebar-text ml-3">Laporan Owner</span>
                <span class="sidebar-tooltip">Laporan Owner</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="relative flex items-center px-4 py-3 rounded-lg hover:bg-emerald-500/20 hover:text-emerald-400 transition {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-600 dark:text-slate-300' }}">
                <div class="section-icon-wrapper bg-emerald-500/15 text-emerald-500"><i class="fa-solid fa-receipt"></i></div>
                <span class="sidebar-text ml-3">Daftar Pesanan</span>
                <span class="sidebar-tooltip">Daftar Pesanan</span>
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'owner', 'hr']))
            <div class="pt-2 pb-1">
                @php $hrActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.hr.*') || request()->routeIs('admin.attendances.*'); @endphp
                <button onclick="document.getElementById('submenu-hr').classList.toggle('hidden'); document.getElementById('icon-hr').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-purple-500 dark:hover:text-purple-400 transition rounded-lg hover:bg-purple-500/10">
                    <div class="section-icon-wrapper bg-purple-500/15 text-purple-500"><i class="fa-solid fa-users-gear"></i></div>
                    <span class="section-label flex-grow text-left">Manajemen SDM (HR)</span>
                    <i id="icon-hr" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $hrActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">SDM (HR)</span>
                </button>
                <div id="submenu-hr" class="space-y-1 {{ $hrActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.users.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-users"></i></div><span class="sidebar-text ml-2">Data Pegawai</span><span class="sidebar-tooltip">Data Pegawai</span></a>
                    <a href="{{ route('admin.hr.shifts') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.hr.shifts') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-clock"></i></div><span class="sidebar-text ml-2">Master Shift</span><span class="sidebar-tooltip">Master Shift</span></a>
                    <a href="{{ route('admin.hr.schedules') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.hr.schedules') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-calendar-days"></i></div><span class="sidebar-text ml-2">Jadwal Shift</span><span class="sidebar-tooltip">Jadwal Shift</span></a>
                    <a href="{{ route('admin.attendances.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.attendances.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-fingerprint"></i></div><span class="sidebar-text ml-2">Absensi</span><span class="sidebar-tooltip">Absensi</span></a>
                    <a href="{{ route('admin.hr.leaves') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.hr.leaves') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-envelope-open-text"></i></div><span class="sidebar-text ml-2">Cuti & Izin</span><span class="sidebar-tooltip">Cuti & Izin</span></a>
                    <a href="{{ route('admin.hr.components') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.hr.components') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-money-bill-transfer"></i></div><span class="sidebar-text ml-2">Komponen Gaji</span><span class="sidebar-tooltip">Komponen Gaji</span></a>
                    <a href="{{ route('admin.hr.payroll') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.hr.payroll') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-file-invoice-dollar"></i></div><span class="sidebar-text ml-2">Penggajian</span><span class="sidebar-tooltip">Penggajian</span></a>
                    <a href="{{ route('admin.settings.attendance') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.settings.attendance') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-purple-500/10 text-purple-400"><i class="fa-solid fa-map-location-dot"></i></div><span class="sidebar-text ml-2">Pengaturan Absensi</span><span class="sidebar-tooltip">Pengaturan Absensi</span></a>
                </div>
            </div>
            @endif
            @if(in_array(auth()->user()->role, ['admin', 'owner']))
            <div class="pt-2 pb-1">
                @php $menuActive = request()->routeIs('admin.categories.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.variants.*') || request()->routeIs('admin.packages.*'); @endphp
                <button onclick="document.getElementById('submenu-menu').classList.toggle('hidden'); document.getElementById('icon-menu').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-amber-500 dark:hover:text-amber-400 transition rounded-lg hover:bg-amber-500/10">
                    <div class="section-icon-wrapper bg-amber-500/15 text-amber-500"><i class="fa-solid fa-utensils"></i></div>
                    <span class="section-label flex-grow text-left">Manajemen Menu</span>
                    <i id="icon-menu" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $menuActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Menu</span>
                </button>
                <div id="submenu-menu" class="space-y-1 {{ $menuActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.categories.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-amber-500/10 text-amber-400"><i class="fa-solid fa-tags"></i></div><span class="sidebar-text ml-2">Kategori Menu</span><span class="sidebar-tooltip">Kategori</span></a>
                    <a href="{{ route('admin.products.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-amber-500/10 text-amber-400"><i class="fa-solid fa-burger"></i></div><span class="sidebar-text ml-2">Data Produk</span><span class="sidebar-tooltip">Produk</span></a>
                    <a href="{{ route('admin.variants.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.variants.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-amber-500/10 text-amber-400"><i class="fa-solid fa-layer-group"></i></div><span class="sidebar-text ml-2">Varian Menu</span><span class="sidebar-tooltip">Varian</span></a>
                    <a href="{{ route('admin.packages.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.packages.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-amber-500/10 text-amber-400"><i class="fa-solid fa-box-open"></i></div><span class="sidebar-text ml-2">Paket Menu</span><span class="sidebar-tooltip">Paket</span></a>
                </div>
            </div>
            @endif
            @if(in_array(auth()->user()->role, ['admin', 'inventory', 'kitchen']))
            <div class="pt-2 pb-1">
                @php $invActive = request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.ingredients.*') || request()->routeIs('admin.purchase_orders.*') || request()->routeIs('admin.goods_receipts.*') || request()->routeIs('admin.stock_opnames.*') || request()->routeIs('admin.stock_transfers.*') || request()->routeIs('admin.stock_mutations.*') || request()->routeIs('admin.wastes.*') || request()->routeIs('admin.recipes.*'); @endphp
                <button onclick="document.getElementById('submenu-inv').classList.toggle('hidden'); document.getElementById('icon-inv').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-emerald-500 dark:hover:text-emerald-400 transition rounded-lg hover:bg-emerald-500/10">
                    <div class="section-icon-wrapper bg-emerald-500/15 text-emerald-500"><i class="fa-solid fa-warehouse"></i></div>
                    <span class="section-label flex-grow text-left">Manajemen Inventory</span>
                    <i id="icon-inv" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $invActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Inventory</span>
                </button>
                <div id="submenu-inv" class="space-y-1 {{ $invActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.suppliers.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.suppliers.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-truck-field"></i></div><span class="sidebar-text ml-2">Supplier</span><span class="sidebar-tooltip">Supplier</span></a>
                    <a href="{{ route('admin.ingredients.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.ingredients.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-box"></i></div><span class="sidebar-text ml-2">Bahan Baku</span><span class="sidebar-tooltip">Bahan Baku</span></a>
                    <a href="{{ route('admin.purchase_orders.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.purchase_orders.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-file-invoice"></i></div><span class="sidebar-text ml-2">Purchase Order</span><span class="sidebar-tooltip">PO</span></a>
                    <a href="{{ route('admin.goods_receipts.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.goods_receipts.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-boxes-packing"></i></div><span class="sidebar-text ml-2">Penerimaan Barang</span><span class="sidebar-tooltip">Penerimaan</span></a>
                    <a href="{{ route('admin.stock_opnames.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.stock_opnames.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-clipboard-check"></i></div><span class="sidebar-text ml-2">Stok Opname</span><span class="sidebar-tooltip">Opname</span></a>
                    <a href="{{ route('admin.stock_transfers.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.stock_transfers.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-truck-ramp-box"></i></div><span class="sidebar-text ml-2">Transfer Stok</span><span class="sidebar-tooltip">Transfer</span></a>
                    <a href="{{ route('admin.stock_mutations.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.stock_mutations.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-right-left"></i></div><span class="sidebar-text ml-2">Mutasi Stok</span><span class="sidebar-tooltip">Mutasi</span></a>
                    <a href="{{ route('admin.wastes.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.wastes.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-trash-can"></i></div><span class="sidebar-text ml-2">Manajemen Waste</span><span class="sidebar-tooltip">Waste</span></a>
                    <a href="{{ route('admin.recipes.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.recipes.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-emerald-500/10 text-emerald-400"><i class="fa-solid fa-book-open"></i></div><span class="sidebar-text ml-2">Resep BOM</span><span class="sidebar-tooltip">Resep</span></a>
                </div>
            </div>
            @endif
            @if(in_array(auth()->user()->role, ['admin', 'owner']))
            <div class="pt-2 pb-1">
                @php $tableActive = request()->routeIs('admin.areas.*') || request()->routeIs('admin.tables.*') || request()->routeIs('admin.reservations.*') || request()->routeIs('pos.menu'); @endphp
                <button onclick="document.getElementById('submenu-table').classList.toggle('hidden'); document.getElementById('icon-table').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-blue-500 dark:hover:text-blue-400 transition rounded-lg hover:bg-blue-500/10">
                    <div class="section-icon-wrapper bg-blue-500/15 text-blue-500"><i class="fa-solid fa-chair"></i></div>
                    <span class="section-label flex-grow text-left">Manajemen Meja</span>
                    <i id="icon-table" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $tableActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Meja</span>
                </button>
                <div id="submenu-table" class="space-y-1 {{ $tableActive ? '' : 'hidden' }}">
                    <a href="{{ route('pos.menu') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('pos.menu') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-blue-500/10 text-blue-400"><i class="fa-solid fa-cash-register"></i></div><span class="sidebar-text ml-2">Pesanan Meja & POS</span><span class="sidebar-tooltip">POS</span></a>
                    <a href="{{ route('admin.areas.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.areas.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-blue-500/10 text-blue-400"><i class="fa-solid fa-map-location-dot"></i></div><span class="sidebar-text ml-2">Area Meja</span><span class="sidebar-tooltip">Area</span></a>
                    <a href="{{ route('admin.tables.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.tables.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-blue-500/10 text-blue-400"><i class="fa-solid fa-border-all"></i></div><span class="sidebar-text ml-2">Pengaturan Meja</span><span class="sidebar-tooltip">Meja</span></a>
                    <a href="{{ route('admin.reservations.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.reservations.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-blue-500/10 text-blue-400"><i class="fa-solid fa-calendar-check"></i></div><span class="sidebar-text ml-2">Reservasi</span><span class="sidebar-tooltip">Reservasi</span></a>
                </div>
            </div>
            
            <div class="pt-2 pb-1">
                @php $custActive = request()->routeIs('admin.customers.*') || request()->routeIs('admin.settings.loyalty') || request()->routeIs('admin.settings.birthday'); @endphp
                <button onclick="document.getElementById('submenu-cust').classList.toggle('hidden'); document.getElementById('icon-cust').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-pink-500 dark:hover:text-pink-400 transition rounded-lg hover:bg-pink-500/10">
                    <div class="section-icon-wrapper bg-pink-500/15 text-pink-500"><i class="fa-solid fa-heart"></i></div>
                    <span class="section-label flex-grow text-left">Pelanggan (Member)</span>
                    <i id="icon-cust" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $custActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Pelanggan</span>
                </button>
                <div id="submenu-cust" class="space-y-1 {{ $custActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.customers.index') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.customers.*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-pink-500/10 text-pink-400"><i class="fa-solid fa-address-book"></i></div><span class="sidebar-text ml-2">Data Pelanggan</span><span class="sidebar-tooltip">Pelanggan</span></a>
                    <a href="{{ route('admin.settings.loyalty') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.settings.loyalty') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-pink-500/10 text-pink-400"><i class="fa-solid fa-gears"></i></div><span class="sidebar-text ml-2">Aturan Poin</span><span class="sidebar-tooltip">Poin</span></a>
                    <a href="{{ route('admin.settings.birthday') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.settings.birthday') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-pink-500/10 text-pink-400"><i class="fa-solid fa-gift"></i></div><span class="sidebar-text ml-2">Promo Ultah</span><span class="sidebar-tooltip">Promo Ultah</span></a>
                </div>
            </div>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'owner', 'finance', 'hr']))
            <div class="pt-2 pb-1">
                @php $financeActive = request()->routeIs('admin.finance.*') || request()->routeIs('admin.pos.sales_report'); @endphp
                <button onclick="document.getElementById('submenu-finance').classList.toggle('hidden'); document.getElementById('icon-finance').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-cyan-500 dark:hover:text-cyan-400 transition rounded-lg hover:bg-cyan-500/10">
                    <div class="section-icon-wrapper bg-cyan-500/15 text-cyan-500"><i class="fa-solid fa-coins"></i></div>
                    <span class="section-label flex-grow text-left">Keuangan & Laporan</span>
                    <i id="icon-finance" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $financeActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Keuangan</span>
                </button>
                <div id="submenu-finance" class="space-y-1 {{ $financeActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.finance.dashboard') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.finance.dashboard') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-cyan-500/10 text-cyan-400"><i class="fa-solid fa-chart-line"></i></div><span class="sidebar-text ml-2">Dashboard</span><span class="sidebar-tooltip">Dashboard</span></a>
                    <a href="{{ route('admin.finance.expenses') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.finance.expenses') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-cyan-500/10 text-cyan-400"><i class="fa-solid fa-money-bill-transfer"></i></div><span class="sidebar-text ml-2">Biaya Operasional</span><span class="sidebar-tooltip">Biaya</span></a>
                    <a href="{{ route('admin.finance.report') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.finance.report') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-cyan-500/10 text-cyan-400"><i class="fa-solid fa-file-invoice-dollar"></i></div><span class="sidebar-text ml-2">Laporan Laba Rugi</span><span class="sidebar-tooltip">Laba Rugi</span></a>
                    <a href="{{ route('admin.pos.sales_report') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.pos.sales_report') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-cyan-500/10 text-cyan-400"><i class="fa-solid fa-chart-bar"></i></div><span class="sidebar-text ml-2">Laporan Penjualan</span><span class="sidebar-tooltip">Penjualan</span></a>
                </div>
            </div>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'owner']))
            <div class="pt-2 pb-1">
                @php $cafeActive = request()->routeIs('admin.cafe.*'); @endphp
                <button onclick="document.getElementById('submenu-cafe').classList.toggle('hidden'); document.getElementById('icon-cafe').classList.toggle('rotate-90')" class="section-btn relative w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1 hover:text-brand-500 dark:hover:text-brand-400 transition rounded-lg hover:bg-brand-500/10">
                    <div class="section-icon-wrapper bg-brand-500/15 text-brand-500"><i class="fa-solid fa-mug-saucer"></i></div>
                    <span class="section-label flex-grow text-left">Pengaturan Kafe</span>
                    <i id="icon-cafe" class="fa-solid fa-chevron-right transition-transform duration-200 {{ $cafeActive ? 'rotate-90' : '' }}"></i>
                    <span class="sidebar-tooltip">Kafe</span>
                </button>
                <div id="submenu-cafe" class="space-y-1 {{ $cafeActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.cafe.profile') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.cafe.profile*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-brand-500/10 text-brand-400"><i class="fa-solid fa-store"></i></div><span class="sidebar-text ml-2">Profil Kafe</span><span class="sidebar-tooltip">Profil</span></a>
                    <a href="{{ route('admin.cafe.units') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.cafe.units*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-brand-500/10 text-brand-400"><i class="fa-solid fa-weight-scale"></i></div><span class="sidebar-text ml-2">Master Satuan</span><span class="sidebar-tooltip">Satuan</span></a>
                    <a href="{{ route('admin.cafe.payment_methods') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.cafe.payment_methods*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-brand-500/10 text-brand-400"><i class="fa-solid fa-wallet"></i></div><span class="sidebar-text ml-2">Metode Pembayaran</span><span class="sidebar-tooltip">Pembayaran</span></a>
                    <a href="{{ route('admin.cafe.tax') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.cafe.tax*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-brand-500/10 text-brand-400"><i class="fa-solid fa-receipt"></i></div><span class="sidebar-text ml-2">Konfigurasi Pajak</span><span class="sidebar-tooltip">Pajak</span></a>
                    <a href="{{ route('admin.cafe.payment_settings') }}" class="relative flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('admin.cafe.payment_settings*') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }} ml-2"><div class="section-icon-wrapper bg-brand-500/10 text-brand-400"><i class="fa-solid fa-credit-card"></i></div><span class="sidebar-text ml-2">Gerbang Pembayaran</span><span class="sidebar-tooltip">Gerbang</span></a>
                </div>
            </div>
            @endif

            <div class="mt-4 border-t border-slate-700 pt-4 space-y-1">
                @if(in_array(auth()->user()->role, ['admin', 'kitchen']))
                <a href="{{ route('kitchen.index') }}" class="relative flex items-center px-4 py-3 rounded-lg hover:bg-orange-500/20 hover:text-orange-400 transition text-slate-600 dark:text-slate-300">
                    <div class="section-icon-wrapper bg-orange-500/15 text-orange-500"><i class="fa-solid fa-fire-burner"></i></div>
                    <span class="sidebar-text ml-3">Layar Dapur (KDS)</span>
                    <span class="sidebar-tooltip">Dapur</span>
                </a>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'cashier', 'owner']))
                <a href="{{ route('pos.menu') }}" class="relative flex items-center px-4 py-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition {{ request()->routeIs('pos.menu') ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }}">
                    <div class="section-icon-wrapper bg-emerald-500/15 text-emerald-500"><i class="fa-solid fa-cash-register"></i></div>
                    <span class="sidebar-text ml-3">Go to POS / Pesanan Meja</span>
                    <span class="sidebar-tooltip">POS / Pesanan Meja</span>
                </a>
                @endif
            </div>
        </nav>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="relative flex items-center w-full text-left px-4 py-2 text-red-400 hover:bg-red-500/20 rounded-lg transition">
                    <div class="section-icon-wrapper bg-red-500/15 text-red-400"><i class="fa-solid fa-right-from-bracket"></i></div>
                    <span class="sidebar-text ml-3">Logout</span>
                    <span class="sidebar-tooltip">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="flex-grow w-full md:ml-64 p-4 md:p-8 pt-20 md:pt-20 min-h-screen transition-all duration-300">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500 text-red-700 dark:text-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth < 768) {
                // Mobile behavior (Drawer toggle)
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            } else {
                // Desktop behavior (Collapse/Shrink)
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('collapsed');
                
                mainContent.classList.toggle('md:ml-64');
                mainContent.classList.toggle('collapsed-main');
            }
        }

        function handleAutoCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth >= 768 && window.innerWidth < 1024) {
                if (sidebar.classList.contains('w-64')) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('collapsed');
                    mainContent.classList.remove('md:ml-64');
                    mainContent.classList.add('collapsed-main');
                }
            } else if (window.innerWidth >= 1024) {
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.add('w-64');
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.add('md:ml-64');
                    mainContent.classList.remove('collapsed-main');
                }
            } else {
                // Mobile behavior, ensure drawer classes are correct if resized
                sidebar.classList.add('w-64');
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed-main');
            }
        }

        window.addEventListener('resize', handleAutoCollapse);
        document.addEventListener('DOMContentLoaded', handleAutoCollapse);
        document.addEventListener('turbo:load', handleAutoCollapse);
    </script>
</body>
</html>
