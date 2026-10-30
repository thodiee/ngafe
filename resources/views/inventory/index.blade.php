@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-400 dark:to-teal-500">Inventory & BOM</h1>
            <p class="text-slate-500 dark:text-slate-400">Raw Materials & Bill of Materials</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ingredients Stock -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-boxes-stacked mr-2 text-emerald-500 dark:text-emerald-400"></i> Ingredients Stock</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700">
                                <th class="pb-3">Ingredient Name</th>
                                <th class="pb-3">Current Stock</th>
                                <th class="pb-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingredients as $ing)
                            @php $totalStock = $ing->stock_gudang + $ing->stock_dapur; @endphp
                            <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4">
                                    <div class="font-bold text-[#2c2520] dark:text-slate-200">{{ $ing->name }}</div>
                                    @if($totalStock <= $ing->minimum_stock)
                                        <span class="text-xs text-red-500 dark:text-red-400"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>
                                    @endif
                                </td>
                                <td class="py-4 font-mono">
                                    <div class="text-lg {{ $totalStock <= $ing->minimum_stock ? 'text-red-500 dark:text-red-400 font-bold' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $totalStock }} <span class="text-slate-500 text-sm">{{ $ing->unit }}</span></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Gudang: {{ $ing->stock_gudang }} | Dapur: {{ $ing->stock_dapur }}</div>
                                </td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('admin.ingredients.index') }}" class="bg-slate-100 hover:bg-emerald-600 dark:bg-slate-700 dark:hover:bg-emerald-600 text-slate-700 dark:text-white hover:text-white px-3 py-1 rounded text-sm transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Manage
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-500">No ingredients found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bill of Materials (BOM) -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-list-check mr-2 text-teal-500 dark:text-teal-400"></i> Bill of Materials (BOM)</h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @forelse($products as $prod)
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                        <h4 class="font-bold text-xl text-[#2c2520] dark:text-white mb-3 flex justify-between items-center">
                            {{ $prod->name }}
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400">{{ $prod->category->name ?? 'Uncategorized' }}</span>
                        </h4>
                        @if($prod->recipes->count() > 0)
                            <ul class="space-y-2">
                                @foreach($prod->recipes as $recipe)
                                <li class="flex justify-between items-center text-sm border-b border-slate-200 dark:border-slate-700/50 pb-2">
                                    <span class="text-slate-600 dark:text-slate-300"><i class="fa-solid fa-caret-right text-teal-500 mr-2"></i>{{ $recipe->ingredient->name ?? 'Unknown' }}</span>
                                    <span class="font-mono text-teal-600 dark:text-teal-400 bg-teal-500/10 dark:bg-teal-900/30 px-2 py-0.5 rounded">{{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? '' }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-500 italic">No recipe defined yet.</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">No products found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection