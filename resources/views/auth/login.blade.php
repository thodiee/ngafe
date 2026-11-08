@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center min-h-[70vh] animate-fade-in-up">
    <div class="glass-luxury p-8 rounded-3xl w-full max-w-md shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-600 via-brand-500 to-amber-500"></div>
        <h2 class="text-3xl font-bold mb-6 text-[#2c2520] dark:text-white text-center"><i class="fa-solid fa-mug-hot text-brand-500 mr-2"></i> Ngafe Login</h2>
        
        @if(session('error'))
            <div class="mb-5 p-3.5 rounded-xl bg-red-500/15 border border-red-500/30 text-red-600 dark:text-red-400 text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-500/15 border border-red-500/30 text-red-600 dark:text-red-400 text-xs">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" id="loginForm" data-turbo="false">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Email</label>
                <input type="email" id="emailInput" name="email" value="{{ old('email') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition duration-150" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Password</label>
                <input type="password" id="passwordInput" name="password" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition duration-150" required>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95 text-lg hover:shadow-brand-500/20 mb-6">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk (Sign In)
            </button>
        </form>

        <div class="border-t border-slate-200 dark:border-slate-800 pt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-center mb-3">Akun Demo Cepat (Klik untuk Isi Otomatis)</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <button type="button" onclick="fillDemo('owner@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-medium transition text-center truncate">👑 Owner</button>
                <button type="button" onclick="fillDemo('admin@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-medium transition text-center truncate">⚙️ Admin</button>
                <button type="button" onclick="fillDemo('cashier@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-medium transition text-center truncate">💵 Kasir</button>
                <button type="button" onclick="fillDemo('kitchen@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-orange-500/10 hover:bg-orange-500/20 text-orange-600 dark:text-orange-400 font-medium transition text-center truncate">🍳 Kitchen</button>
                <button type="button" onclick="fillDemo('waiter@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-medium transition text-center truncate">🛎️ Waiter</button>
                <button type="button" onclick="fillDemo('hr@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 font-medium transition text-center truncate">👥 HR</button>
                <button type="button" onclick="fillDemo('finance@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 font-medium transition text-center truncate">📊 Finance</button>
                <button type="button" onclick="fillDemo('inventory@ngafe.com')" class="px-2 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-medium transition text-center truncate">📦 Gudang</button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(email) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';
    }

    // Refresh if loaded from back-forward cache to ensure fresh CSRF token
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
@endsection