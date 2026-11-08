<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ngafe - Solusi manajemen F&B terlengkap terintegrasi POS, HR, Inventaris, dan Rekrutmen karyawan. Nikmati kopi terbaik dan suasana ternyaman.">
    <meta name="keywords" content="cafe, kopi, cafe jakarta, pos cafe, manajemen cafe, workspace cafe, rekrutmen cafe">
    <title>Ngafe - F&B Management</title>
    
    <!-- Anti-Flicker Script for Theme State -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Tailwind CSS -->
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
                            500: '#d4af37', // Gold Main
                            600: '#b08e26',
                            700: '#8c6f1a',
                            900: '#47360a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        /* Premium Adaptive Glassmorphism */
        .glass, .glass-dark, .glass-luxury {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 8px 32px 0 rgba(212, 175, 55, 0.04);
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
        }
        .dark .glass, .dark .glass-dark, .dark .glass-luxury {
            background: rgba(18, 18, 24, 0.75);
            border: 1px solid rgba(212, 175, 55, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        /* Animations & Keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
    
    <!-- Theme Toggle Scripts -->
    <script>
        function updateThemeToggleIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const sunIcon = document.getElementById('themeSunIcon');
            const moonIcon = document.getElementById('themeMoonIcon');
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
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateThemeToggleIcon();
        }
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeToggleIcon();
        });
    </script>
</head>
<body class="bg-[#faf9f6] text-[#2c2520] dark:bg-[#09090d] dark:text-slate-200 antialiased min-h-screen flex flex-col transition-colors duration-200">
    @if(request()->is('self-service*'))
        <nav class="glass-dark sticky top-0 z-50">
            <div class="max-w-md mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-brand-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-mug-hot"></i> Ngafe
                        </span>
                    </div>
                    
                    @php
                        $tableName = '';
                        if (isset($table)) {
                            $tableName = $table->table_number;
                        } elseif (isset($order) && $order->table) {
                            $tableName = $order->table->table_number;
                        }
                    @endphp
                    @if($tableName)
                        <div class="bg-brand-500/10 text-brand-500 px-3 py-1 rounded-full text-xs font-bold border border-brand-500/20 uppercase tracking-wider">
                            Meja {{ $tableName }}
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <button onclick="toggleTheme()" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-brand-500 hover:text-brand-600 transition" id="themeToggleBtn" title="Ganti Tema">
                            <i class="fa-solid fa-sun text-amber-500" id="themeSunIcon"></i>
                            <i class="fa-solid fa-moon text-brand-400 hidden" id="themeMoonIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    @else
        <nav class="glass-dark sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center gap-2 text-xl font-bold text-brand-500">
                            <i class="fa-solid fa-mug-hot"></i> Ngafe
                        </a>
                    </div>
                    
                    @if(request()->is('/') || request()->is('careers*') || request()->is('login'))
                    <!-- Landing Page Navbar -->
                    <div class="hidden lg:flex items-center gap-6">
                        <a href="{{ request()->is('/') ? '#' : url('/') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Beranda</a>
                        <a href="{{ request()->is('/') ? '#about-section' : url('/#about-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Tentang</a>
                        <a href="{{ request()->is('/') ? '#menu-section' : url('/#menu-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Menu</a>
                        <a href="{{ request()->is('/') ? '#gallery-section' : url('/#gallery-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Galeri</a>
                        <a href="{{ request()->is('/') ? '#testimonial-section' : url('/#testimonial-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Testimoni</a>
                        <a href="{{ request()->is('/') ? '#contact-section' : url('/#contact-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">Lokasi</a>
                        <a href="{{ request()->is('/') ? '#faq-section' : url('/#faq-section') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-500 dark:hover:text-brand-400 transition">FAQ</a>
                        <a href="{{ route('careers.jobs') }}" class="text-sm font-semibold {{ request()->is('careers*') ? 'text-brand-500 dark:text-brand-400' : 'text-slate-700 dark:text-slate-300' }} hover:text-brand-500 dark:hover:text-brand-400 transition">Recruitment</a>
                    </div>
                    @endif

                    <div class="flex items-center gap-3">
                        @if(request()->is('/') || request()->is('careers*') || request()->is('login'))
                            <a href="{{ request()->is('/') ? '#contact-section' : url('/#contact-section') }}" class="hidden sm:inline-flex items-center justify-center px-6 py-2 text-sm font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500 hover:text-white border border-emerald-500/20 rounded-full transition-all duration-300">
                                Hubungi Kami
                            </a>
                        @else
                            @auth
                                @if(in_array(auth()->user()->role, ['admin', 'cashier', 'owner']))
                                    <a href="{{ route('pos.menu') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-cash-register text-blue-500 dark:text-blue-400"></i> POS</a>
                                @endif

                                @if(in_array(auth()->user()->role, ['admin', 'waiter']))
                                    <a href="{{ route('waiter.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-bell-concierge text-yellow-500 dark:text-yellow-400"></i> Waiter</a>
                                @endif

                                @if(in_array(auth()->user()->role, ['admin', 'kitchen']))
                                    <a href="{{ route('kitchen.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-fire-burner text-red-500 dark:text-red-400"></i> Dapur</a>
                                @endif
                                <a href="{{ route('hr.dashboard') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-fingerprint text-purple-500 dark:text-purple-400"></i> Absensi</a>

                                @if(in_array(auth()->user()->role, ['admin', 'inventory', 'kitchen']))
                                    <a href="{{ route('inventory.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-boxes-stacked text-emerald-500 dark:text-emerald-400"></i> Inventory</a>
                                @endif
                            @endauth
                            
                            <a href="{{ route('careers.jobs') }}" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600"><i class="fa-solid fa-briefcase text-orange-500 dark:orange-400"></i> Careers</a>
                        @endif
                        
                        <!-- Theme Toggle Switch -->
                        <button onclick="toggleTheme()" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-brand-500 hover:text-brand-600 transition" id="themeToggleBtn" title="Ganti Tema">
                            <i class="fa-solid fa-sun text-amber-500" id="themeSunIcon"></i>
                            <i class="fa-solid fa-moon text-brand-400 hidden" id="themeMoonIcon"></i>
                        </button>
                        
                        <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div> <!-- Separator -->
                        
                        @auth
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white focus:outline-none transition bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-user-circle text-brand-500"></i>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50 transform origin-top-right group-hover:scale-100 scale-95">
                                @php
                                    $role = auth()->user()->role;
                                    $dashRoute = route('admin.dashboard');
                                    $dashLabel = 'Ngafe Admin';
                                    $dashIcon = 'fa-gauge';

                                    if ($role === 'cashier') {
                                        $dashRoute = route('pos.menu');
                                        $dashLabel = 'Kasir POS';
                                        $dashIcon = 'fa-cash-register';
                                    } elseif ($role === 'waiter') {
                                        $dashRoute = route('waiter.index');
                                        $dashLabel = 'Layar Waiter';
                                        $dashIcon = 'fa-bell-concierge';
                                    } elseif ($role === 'kitchen') {
                                        $dashRoute = route('kitchen.index');
                                        $dashLabel = 'Layar Dapur';
                                        $dashIcon = 'fa-fire-burner';
                                    } elseif ($role === 'hr') {
                                        $dashRoute = route('admin.users.index');
                                        $dashLabel = 'Portal HR';
                                        $dashIcon = 'fa-users-gear';
                                    } elseif ($role === 'inventory') {
                                        $dashRoute = route('inventory.index');
                                        $dashLabel = 'Inventaris';
                                        $dashIcon = 'fa-warehouse';
                                    } elseif ($role === 'finance') {
                                        $dashRoute = route('admin.finance.dashboard');
                                        $dashLabel = 'Keuangan';
                                        $dashIcon = 'fa-coins';
                                    }
                                @endphp
                                <a href="{{ $dashRoute }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition">
                                    <i class="fa-solid {{ $dashIcon }} w-5 text-center mr-2 text-brand-500"></i> {{ $dashLabel }}
                                </a>
                                <div class="h-px bg-slate-200 dark:bg-slate-700 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST" class="block w-full text-left">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-red-600 dark:hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket w-5 text-center mr-2"></i> Logout</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition bg-brand-500/10 px-4 py-1.5 rounded-full border border-brand-500/20"><i class="fa-solid fa-right-to-bracket mr-1"></i> Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    @endif
    
    <main class="flex-grow {{ request()->is('self-service*') ? 'max-w-md mx-auto w-full px-4 py-6' : 'max-w-7xl mx-auto w-full px-8 sm:px-12 lg:px-16 py-8' }}">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500 text-red-700 dark:text-red-200">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
    
    <footer class="glass-dark mt-auto py-6 text-center text-slate-400 text-sm">
        <p>&copy; {{ date('Y') }} Ngafe Management System. Designed for scale.</p>
    </footer>

    @auth
    @php
        $todaySchedule = \App\Models\Schedule::with('shift')
            ->where('user_id', auth()->id())
            ->where('date', today()->format('Y-m-d'))
            ->first();
    @endphp

    @if($todaySchedule && $todaySchedule->shift)
    <script>
        let shiftNotified = false;
        const shiftEndTimeStr = "{{ today()->format('Y-m-d') }} {{ $todaySchedule->shift->end_time }}";
        const shiftEnd = new Date(shiftEndTimeStr).getTime();

        setInterval(() => {
            if (shiftNotified) return;

            const now = new Date().getTime();
            const timeDiff = shiftEnd - now;
            const minutesLeft = Math.floor(timeDiff / (1000 * 60));

            // Jika kurang dari atau sama dengan 10 menit, tapi belum minus (belum lewat jauh)
            if (minutesLeft <= 10 && minutesLeft >= 0) {
                shiftNotified = true;
                
                // Buat custom modal/alert UI agar tidak mengganggu flow (bisa di close)
                const alertHtml = `
                    <div id="shiftWarning" class="fixed bottom-4 right-4 bg-orange-500 text-white p-6 rounded-xl shadow-2xl z-50 max-w-sm flex flex-col gap-3 border border-orange-400 animate-bounce">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            <div>
                                <h4 class="font-bold text-lg">Peringatan Shift Berakhir!</h4>
                                <p class="text-sm">Shift Anda akan berakhir dalam ${minutesLeft} menit. Mohon bersiap untuk melakukan rekap, operan tugas, atau Tutup Shift.</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('shiftWarning').remove()" class="bg-orange-700 hover:bg-orange-800 px-4 py-2 rounded font-bold w-full mt-2">Mengerti</button>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', alertHtml);
            }
        }, 30000); // Cek setiap 30 detik
    </script>
    @endif
    @endauth
</body>
</html>