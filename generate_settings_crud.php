<?php
$controllerCode = <<<EOT
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index() {
        \$earn_rate = Setting::firstOrCreate(['key' => 'point_earn_threshold'], ['value' => '10000', 'type' => 'int']);
        \$redeem_value = Setting::firstOrCreate(['key' => 'point_redeem_value'], ['value' => '100', 'type' => 'int']);
        return view('admin.settings.loyalty', compact('earn_rate', 'redeem_value'));
    }

    public function update(Request \$request) {
        \$request->validate([
            'point_earn_threshold' => 'required|numeric|min:1',
            'point_redeem_value' => 'required|numeric|min:1',
        ]);

        Setting::where('key', 'point_earn_threshold')->update(['value' => \$request->point_earn_threshold]);
        Setting::where('key', 'point_redeem_value')->update(['value' => \$request->point_redeem_value]);

        return redirect()->back()->with('success', 'Pengaturan Loyalitas berhasil diperbarui!');
    }
}
EOT;

$viewCode = <<<EOT
@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold">Pengaturan Loyalitas Poin</h2>
    <p class="text-slate-400">Atur nilai konversi poin untuk pelanggan di sini.</p>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ route('admin.settings.loyalty.update') }}" method="POST">
        @csrf
        
        <div class="mb-6 p-4 bg-slate-900 border border-brand-500/50 rounded-lg">
            <h3 class="font-bold text-lg mb-2 text-brand-400">Aturan Mendapatkan Poin</h3>
            <p class="text-sm text-slate-400 mb-4">Setiap pelanggan belanja kelipatan jumlah di bawah ini, mereka akan mendapatkan 1 Poin.</p>
            <label class="block text-slate-400 mb-2 font-semibold">Batas Belanja per 1 Poin (Rp)</label>
            <div class="relative">
                <span class="absolute left-4 top-2 text-slate-400">Rp</span>
                <input type="number" name="point_earn_threshold" value="{{ \$earn_rate->value }}" class="w-full bg-slate-800 border border-slate-600 rounded-lg pl-10 pr-4 py-2 text-white focus:outline-none focus:border-brand-500" required>
            </div>
            <p class="text-xs text-slate-500 mt-2">Contoh: 10000. Artinya setiap belanja Rp 10.000 dapat 1 Poin.</p>
        </div>
        
        <div class="mb-6 p-4 bg-slate-900 border border-emerald-500/50 rounded-lg">
            <h3 class="font-bold text-lg mb-2 text-emerald-400">Aturan Menukarkan Poin</h3>
            <p class="text-sm text-slate-400 mb-4">Berapa nilai rupiah diskon untuk setiap 1 Poin yang ditukarkan.</p>
            <label class="block text-slate-400 mb-2 font-semibold">Nilai Potongan per 1 Poin (Rp)</label>
            <div class="relative">
                <span class="absolute left-4 top-2 text-slate-400">Rp</span>
                <input type="number" name="point_redeem_value" value="{{ \$redeem_value->value }}" class="w-full bg-slate-800 border border-slate-600 rounded-lg pl-10 pr-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
            </div>
            <p class="text-xs text-slate-500 mt-2">Contoh: 100. Artinya jika menukar 100 poin, akan mendapat diskon Rp 10.000.</p>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-3 rounded-xl transition font-bold w-full">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
EOT;

file_put_contents('app/Http/Controllers/Admin/SettingController.php', $controllerCode);
@mkdir('resources/views/admin/settings', 0777, true);
file_put_contents('resources/views/admin/settings/loyalty.blade.php', $viewCode);

echo "Settings CRUD created.\n";
