@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center min-h-[70vh] animate-fade-in-up">
    <div class="glass-luxury p-8 rounded-3xl w-full max-w-md shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-600 via-brand-500 to-amber-500"></div>
        <h2 class="text-3xl font-bold mb-6 text-[#2c2520] dark:text-white text-center"><i class="fa-solid fa-mug-hot text-brand-500 mr-2"></i> Ngafe Login</h2>
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition duration-150" required>
            </div>
            <div class="mb-8">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition duration-150" required>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95 text-lg hover:shadow-brand-500/20">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Sign In
            </button>
        </form>
    </div>
</div>
@endsection