@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500">Stok Bahan Baku & Resep BOM</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Monitoring stok bahan baku dan komposisi racikan menu secara real-time</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.ingredients.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 transition whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Kelola Bahan Baku
            </a>
            <a href="{{ route('admin.recipes.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition whitespace-nowrap">
                <i class="fa-solid fa-book-open"></i> Resep BOM
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Ingredients Stock -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-base sm:text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-boxes-stacked mr-2 text-emerald-500 dark:text-emerald-400"></i> Stok Bahan Baku</h3>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">{{ $ingredients->count() }} Bahan</span>
            </div>
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[460px] whitespace-nowrap">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                                <th class="pb-3">Nama Bahan</th>
                                <th class="pb-3">Total Stok</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @forelse($ingredients as $ing)
                            @php $totalStock = $ing->stock_gudang + $ing->stock_dapur; @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4">
                                    <div class="font-bold text-[#2c2520] dark:text-slate-200">{{ $ing->name }}</div>
                                    @if($totalStock <= $ing->minimum_stock)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded-full mt-1 border border-rose-500/20">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Menipis (Min: {{ $ing->minimum_stock }})
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 font-mono">
                                    <div class="text-base font-bold {{ $totalStock <= $ing->minimum_stock ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        {{ $totalStock }} <span class="text-slate-500 dark:text-slate-400 text-xs font-normal">{{ $ing->unit }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Gudang: {{ $ing->stock_gudang }} &bull; Dapur: {{ $ing->stock_dapur }}</div>
                                </td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('admin.ingredients.index') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-emerald-600 dark:bg-slate-700 dark:hover:bg-emerald-600 text-slate-700 dark:text-slate-200 hover:text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        <i class="fa-solid fa-pen-to-square"></i> Kelola
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-500">Belum ada bahan baku terdaftar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bill of Materials (BOM) -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-list-check mr-2 text-teal-500 dark:text-teal-400"></i> Komposisi Resep (BOM)</h3>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">{{ $products->count() }} Menu</span>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar pr-1">
                    @forelse($products as $prod)
                    <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700/80 hover:border-teal-500/40 transition">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-base text-[#2c2520] dark:text-white">
                                {{ $prod->name }}
                            </h4>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/60 px-2 py-0.5 rounded-full">{{ $prod->category->name ?? 'Menu' }}</span>
                        </div>
                        @if($prod->recipes->count() > 0)
                            <ul class="space-y-2">
                                @foreach($prod->recipes as $recipe)
                                <li class="flex justify-between items-center text-xs border-b border-slate-100 dark:border-slate-700/40 pb-1.5 last:border-b-0 last:pb-0">
                                    <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-dot text-[8px] text-teal-500"></i>
                                        {{ $recipe->ingredient->name ?? 'Bahan' }}
                                    </span>
                                    <span class="font-mono font-bold text-teal-600 dark:text-teal-400 bg-teal-500/10 dark:bg-teal-900/30 px-2 py-0.5 rounded text-[11px]">{{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? '' }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-slate-400 italic">Belum ada resep bahan racikan.</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">Belum ada menu produk terdaftar</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection