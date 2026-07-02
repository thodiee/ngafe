@forelse($unpaidOrders as $tblOrder)
    <div class="p-4 rounded-2xl bg-white/60 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3 shadow-sm hover:border-brand-500 dark:hover:border-brand-400 transition">
        <div class="flex justify-between items-start">
            <div>
                <h4 class="font-extrabold text-[#2c2520] dark:text-white text-sm uppercase">Meja {{ $tblOrder->table->table_number ?? 'Dine In' }}</h4>
                <span class="text-[9px] text-slate-400 font-mono block">{{ $tblOrder->order_reference }}</span>
            </div>
            <span class="text-brand-600 dark:text-brand-400 font-extrabold text-sm">Rp {{ number_format($tblOrder->total_amount, 0, ',', '.') }}</span>
        </div>
        
        <!-- Items list -->
        <div class="border-t border-slate-100 dark:border-slate-750 pt-2 space-y-1.5">
            @foreach($tblOrder->items as $oItem)
                <div class="text-xs text-slate-700 dark:text-slate-300 leading-tight">
                    <strong>{{ $oItem->product->name }}</strong> <span class="text-slate-400 font-mono text-[10px]">x{{ $oItem->quantity }}</span>
                    @if($oItem->variants->count() > 0)
                        <span class="text-[9px] text-slate-400 block font-normal">({{ $oItem->variants->pluck('option_name')->join(', ') }})</span>
                    @endif
                    @if($oItem->notes)
                        <span class="text-[9px] text-orange-500 dark:text-orange-400 italic block font-medium font-mono">"{{ $oItem->notes }}"</span>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- Action Buttons: cash / qris -->
        <div class="flex gap-2 border-t border-slate-100 dark:border-slate-700 pt-3">
            <button onclick="confirmTableOrderPayment({{ $tblOrder->id }}, 'cash', '{{ $tblOrder->table->table_number ?? 'Dine In' }}', {{ $tblOrder->total_amount }})" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-[10px] transition flex items-center justify-center gap-1 active:scale-95 shadow-md shadow-emerald-500/10">
                <i class="fa-solid fa-money-bill-wave"></i> Bayar Tunai
            </button>
            <button onclick="confirmTableOrderPayment({{ $tblOrder->id }}, 'qris_gateway', '{{ $tblOrder->table->table_number ?? 'Dine In' }}', {{ $tblOrder->total_amount }})" class="flex-1 bg-brand-500 hover:bg-brand-600 text-white font-bold py-2 px-3 rounded-xl text-[10px] transition flex items-center justify-center gap-1 active:scale-95 shadow-md shadow-brand-500/10">
                <i class="fa-solid fa-qrcode"></i> QRIS POS
            </button>
        </div>
    </div>
@empty
    <div class="text-center text-slate-450 dark:text-slate-500 py-12">
        <i class="fa-solid fa-bell-concierge text-3xl mb-2 block"></i>
        Tidak ada pesanan meja aktif
    </div>
@endforelse
