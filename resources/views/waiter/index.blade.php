@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto py-4 sm:py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-[#2c2520] dark:text-white"><i class="fa-solid fa-bell-concierge text-brand-500 mr-2"></i> Waiter Dashboard</h2>
        <span class="bg-brand-500/20 text-brand-700 dark:text-brand-400 px-3.5 py-1.5 rounded-full font-bold text-xs sm:text-sm shadow-inner shrink-0">
            <span class="w-2 h-2 rounded-full bg-brand-500 dark:bg-brand-400 inline-block mr-2 animate-pulse"></span>
            Pesanan Siap Saji: {{ count($orders) }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/50 p-4 rounded-lg mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($orders as $order)
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col transition hover:shadow-xl hover:border-brand-500/50 dark:hover:border-brand-400/50">
                <div class="bg-slate-50 dark:bg-slate-900 p-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-bold mb-1">#{{ $order->order_reference }}</div>
                        <div class="text-xl font-bold text-[#2c2520] dark:text-white uppercase">{{ $order->table ? $order->table->name : 'Takeaway' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->created_at->diffForHumans() }}</div>
                        <span class="bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-2 py-1 rounded text-xs font-bold mt-1 inline-block">READY</span>
                    </div>
                </div>
                
                <div class="p-4 flex-grow bg-white/50 dark:bg-slate-800/50">
                    <ul class="space-y-3">
                        @foreach($order->items as $item)
                        <li class="flex justify-between items-start border-b border-slate-200 dark:border-slate-700/50 pb-2 last:border-0 last:pb-0">
                            <div>
                                <span class="font-bold text-[#2c2520] dark:text-white">{{ $item->product->name }}</span>
                                @if($item->variants->count() > 0)
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        @foreach($item->variants as $variant)
                                            <span class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded">{{ $variant->option_name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($item->notes)
                                    <div class="text-xs text-amber-600 dark:text-yellow-400 mt-1"><i class="fa-solid fa-note-sticky"></i> {{ $item->notes }}</div>
                                @endif
                            </div>
                            <span class="font-bold bg-slate-100 dark:bg-slate-700 text-[#2c2520] dark:text-white w-6 h-6 flex items-center justify-center rounded text-sm">x{{ $item->quantity }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                    <form action="{{ route('waiter.serve', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95 text-lg flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i> Sajikan ke Pelanggan
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 border-dashed">
                <i class="fa-solid fa-mug-hot text-6xl text-slate-300 dark:text-slate-600 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-500 dark:text-slate-400">Belum ada pesanan yang siap disajikan.</h3>
                <p class="text-slate-400 dark:text-slate-500 mt-2 text-sm">Pesanan yang sudah dimasak dapur akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
