<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - NGAFE</title>
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="30"> <!-- Auto refresh every 30 seconds -->
    
    <!-- Styles for animation -->
    <style>
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
<body class="bg-[#faf9f6] text-[#2c2520] dark:bg-[#09090d] dark:text-slate-300 font-sans min-h-screen transition-colors duration-200 overflow-x-hidden max-w-full">
    
    <!-- Header -->
    <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-3 sm:px-6 py-3.5 sticky top-0 z-50 flex flex-wrap justify-between items-center gap-3 shadow-md transition">
        <div class="flex items-center space-x-2 sm:space-x-3">
            <h1 class="text-lg sm:text-2xl font-bold text-[#2c2520] dark:text-white flex items-center">
                <i class="fa-solid fa-fire-burner text-brand-500 mr-2 animate-pulse"></i>
                <span>Layar Dapur <span class="hidden sm:inline">(KDS)</span></span>
            </h1>
            <span class="bg-brand-500 text-white text-[10px] sm:text-xs px-2 py-0.5 sm:py-1 rounded-full font-bold animate-pulse whitespace-nowrap">Auto 30s</span>
        </div>
        <div class="flex items-center gap-2.5 sm:gap-4">
            <!-- Theme Toggle Switch -->
            <button onclick="toggleTheme()" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-brand-500 hover:text-brand-600 transition" id="themeToggleBtn" title="Ganti Tema">
                <i class="fa-solid fa-sun text-amber-500" id="themeSunIcon"></i>
                <i class="fa-solid fa-moon text-brand-400 hidden" id="themeMoonIcon"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="text-slate-600 dark:text-slate-400 hover:text-[#2c2520] dark:hover:text-white transition font-medium text-xs sm:text-sm flex items-center whitespace-nowrap"><i class="fa-solid fa-arrow-left mr-1"></i> <span class="hidden sm:inline">Kembali ke </span>Admin</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-3.5 sm:p-6 min-w-0 max-w-full">
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg mb-6 shadow-lg">
                <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($orders->count() == 0)
            <div class="flex flex-col items-center justify-center py-20 text-slate-500">
                <i class="fa-solid fa-mug-hot text-6xl mb-4 opacity-50"></i>
                <p class="text-xl font-bold">Tidak ada antrean pesanan.</p>
                <p class="text-sm">Dapur bisa beristirahat sejenak.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($orders as $order)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden flex flex-col transition duration-150 hover:scale-[1.01] hover:shadow-xl hover:border-brand-500/50 dark:hover:border-brand-400/50 animate-fade-in-up">
                    <!-- Card Header -->
                    <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-600 flex justify-between items-start">
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $order->order_reference }}</div>
                            <div class="text-xl font-bold text-[#2c2520] dark:text-white mt-1">
                                {{ $order->order_type == 'dine_in' ? 'Meja ' . ($order->table->table_number ?? '-') : 'Takeaway' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->created_at->diffForHumans() }}</div>
                            <div class="text-xs font-bold px-2 py-1 bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded mt-1 uppercase">{{ str_replace('_', ' ', $order->order_type) }}</div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="p-4 flex-grow text-[#2c2520] dark:text-slate-300">
                        <ul class="space-y-4">
                            @foreach($order->items as $item)
                            <li class="border-b border-slate-200 dark:border-slate-700 pb-3 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-[#2c2520] dark:text-white text-lg">
                                        <span class="text-brand-600 dark:text-brand-400 mr-2">{{ $item->quantity }}x</span>
                                        {{ $item->product->name ?? 'Produk Dihapus' }}
                                    </div>
                                </div>
                                @if($item->variants->count() > 0)
                                    <div class="mt-1 pl-6 text-sm text-slate-500 dark:text-slate-400">
                                        <ul class="list-disc list-inside">
                                            @foreach($item->variants as $variant)
                                                <li>{{ $variant->variant_name }}: <span class="text-[#2c2520] dark:text-white font-medium">{{ $variant->option_name }}</span></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if($item->notes)
                                    <div class="mt-2 pl-6 text-sm text-amber-600 dark:text-amber-400 italic">
                                        <i class="fa-solid fa-comment-dots mr-1"></i> "{{ $item->notes }}"
                                    </div>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
 
                    <!-- Card Footer / Action -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700">
                        <form action="{{ route('kitchen.ready', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow-lg active:scale-[0.97] transition duration-150 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-check-double mr-2"></i> SELESAI
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>