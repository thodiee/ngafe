@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-pink-500/20 text-pink-500 border border-pink-500/30">
                <i class="fa-solid fa-address-book text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Pelanggan & Member</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola database member setia kafe, akumulasi poin loyalitas, dan data ulang tahun.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.customers.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-pink-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pelanggan</span>
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Nama Member</th>
                        <th class="p-4 sm:px-6">No. WhatsApp / HP</th>
                        <th class="p-4 sm:px-6">Alamat</th>
                        <th class="p-4 sm:px-6">Tgl Lahir</th>
                        <th class="p-4 sm:px-6 text-center">Poin Loyalitas</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($data as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-pink-500/15 text-pink-600 dark:text-pink-400 font-bold flex items-center justify-center border border-pink-500/30 text-xs shrink-0">
                                    {{ strtoupper(substr($item->name, 0, 2)) }}
                                </div>
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item->name }}</div>
                            </div>
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-slate-600 dark:text-slate-300">
                            {{ $item->phone }}
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-500 dark:text-slate-400 max-w-xs truncate">
                            {{ $item->address ?? '-' }}
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-600 dark:text-slate-300">
                            @if($item->dob)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fa-solid fa-cake-candles text-pink-500 text-[10px]"></i>
                                    <span>{{ \Carbon\Carbon::parse($item->dob)->format('d M Y') }}</span>
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                <i class="fa-solid fa-coins text-[10px]"></i>
                                <span>{{ number_format($item->points) }} Poin</span>
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.customers.edit', $item) }}" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit Member">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.customers.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data member {{ $item->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Member">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-users-slash text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data pelanggan yang terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection