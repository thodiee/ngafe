<?php
// 1. Create Migration for pos_sessions and order modification
$migrationName = '2026_05_25_140500_create_pos_sessions_table.php';
$migrationContent = <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_sessions', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->dateTime('opening_time');
            \$table->dateTime('closing_time')->nullable();
            \$table->decimal('opening_cash', 15, 2)->default(0);
            \$table->decimal('closing_cash_system', 15, 2)->default(0);
            \$table->decimal('closing_cash_actual', 15, 2)->default(0);
            \$table->decimal('difference', 15, 2)->default(0);
            \$table->enum('status', ['open', 'closed'])->default('open');
            \$table->text('notes')->nullable();
            \$table->timestamps();
        });

        Schema::table('orders', function (Blueprint \$table) {
            \$table->foreignId('pos_session_id')->nullable()->constrained('pos_sessions')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint \$table) {
            \$table->dropForeign(['pos_session_id']);
            \$table->dropColumn('pos_session_id');
        });
        Schema::dropIfExists('pos_sessions');
    }
};
EOT;
file_put_contents("database/migrations/{$migrationName}", $migrationContent);

// 2. Create PosSession Model
$modelContent = <<<EOT
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model {
    protected \$fillable = ['user_id', 'opening_time', 'closing_time', 'opening_cash', 'closing_cash_system', 'closing_cash_actual', 'difference', 'status', 'notes'];
    
    protected \$casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];

    public function user() {
        return \$this->belongsTo(User::class);
    }
    
    public function orders() {
        return \$this->hasMany(Order::class);
    }
}
EOT;
file_put_contents('app/Models/PosSession.php', $modelContent);

// Update Order model (we can assume it's easy to append to PosController)

// 3. Create PosShiftController
$shiftController = <<<EOT
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PosSession;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosShiftController extends Controller {
    public function open() {
        \$activeSession = PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (\$activeSession) {
            return redirect()->route('pos.menu');
        }
        return view('pos.shift.open');
    }

    public function storeOpen(Request \$request) {
        \$request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        \$activeSession = PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (\$activeSession) {
            return redirect()->route('pos.menu');
        }

        PosSession::create([
            'user_id' => auth()->id(),
            'opening_time' => Carbon::now(),
            'opening_cash' => \$request->opening_cash,
            'status' => 'open'
        ]);

        return redirect()->route('pos.menu')->with('success', 'Shift kasir berhasil dibuka!');
    }

    public function close() {
        \$session = PosSession::where('user_id', auth()->id())->where('status', 'open')->firstOrFail();
        return view('pos.shift.close', compact('session'));
    }

    public function storeClose(Request \$request) {
        \$request->validate([
            'closing_cash_actual' => 'required|numeric|min:0'
        ]);

        \$session = PosSession::where('user_id', auth()->id())->where('status', 'open')->firstOrFail();
        
        // Calculate system cash (only cash payments)
        \$cashSales = \$session->orders()->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('total_amount');
        \$expectedCash = \$session->opening_cash + \$cashSales;
        
        \$difference = \$request->closing_cash_actual - \$expectedCash;

        \$session->update([
            'closing_time' => Carbon::now(),
            'closing_cash_system' => \$expectedCash,
            'closing_cash_actual' => \$request->closing_cash_actual,
            'difference' => \$difference,
            'notes' => \$request->notes,
            'status' => 'closed'
        ]);

        return redirect()->route('pos.shift.summary', \$session->id)->with('success', 'Shift berhasil ditutup.');
    }

    public function summary(\$id) {
        \$session = PosSession::with('user')->findOrFail(\$id);
        if (\$session->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        \$totalSales = \$session->orders()->where('payment_status', 'paid')->sum('total_amount');
        \$cashSales = \$session->orders()->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('total_amount');
        \$qrisSales = \$session->orders()->where('payment_status', 'paid')->where('payment_method', 'qris')->sum('total_amount');

        // Products sold during this session
        \$productsSold = OrderItem::whereHas('order', function(\$q) use (\$session) {
            \$q->where('pos_session_id', \$session->id)->where('payment_status', 'paid');
        })
        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
        ->groupBy('product_id')
        ->with('product')
        ->get();

        return view('pos.shift.summary', compact('session', 'totalSales', 'cashSales', 'qrisSales', 'productsSold'));
    }
}
EOT;
file_put_contents('app/Http/Controllers/PosShiftController.php', $shiftController);

// 4. Create SalesReportController (Admin)
$salesReportController = <<<EOT
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class SalesReportController extends Controller {
    public function index(Request \$request) {
        \$month = \$request->month ?? date('m');
        \$year = \$request->year ?? date('Y');

        \$productsSold = OrderItem::whereHas('order', function(\$q) use (\$month, \$year) {
            \$q->whereMonth('created_at', \$month)
              ->whereYear('created_at', \$year)
              ->where('payment_status', 'paid');
        })
        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
        ->groupBy('product_id')
        ->orderBy('total_qty', 'desc')
        ->with('product')
        ->get();

        \$totalItems = \$productsSold->sum('total_qty');
        \$totalRevenue = \$productsSold->sum('total_revenue');

        return view('admin.pos.sales_report', compact('productsSold', 'month', 'year', 'totalItems', 'totalRevenue'));
    }
}
EOT;
@mkdir('app/Http/Controllers/Admin', 0777, true);
file_put_contents('app/Http/Controllers/Admin/SalesReportController.php', $salesReportController);

// 5. Create Views
@mkdir('resources/views/pos/shift', 0777, true);
@mkdir('resources/views/admin/pos', 0777, true);

$openShiftView = <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-12 bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
    <div class="text-center mb-8">
        <div class="inline-block p-4 rounded-full bg-emerald-500/20 text-emerald-500 mb-4">
            <i class="fa-solid fa-cash-register text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white">Buka Shift Kasir</h2>
        <p class="text-slate-400 text-sm mt-2">Anda harus memasukkan modal awal laci sebelum memulai pesanan.</p>
    </div>
    
    <form action="{{ route('pos.shift.storeOpen') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Modal Awal / Uang Kembalian (Rp)</label>
            <input type="number" name="opening_cash" required min="0" placeholder="Contoh: 100000" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-lg focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 outline-none transition">
        </div>
        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-xl shadow-lg transition duration-300">
            <i class="fa-solid fa-lock-open mr-2"></i> Buka Kasir Sekarang
        </button>
    </form>
</div>
@endsection
EOT;
file_put_contents('resources/views/pos/shift/open.blade.php', $openShiftView);

$closeShiftView = <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-12 bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
    <div class="text-center mb-8">
        <div class="inline-block p-4 rounded-full bg-red-500/20 text-red-500 mb-4">
            <i class="fa-solid fa-lock text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white">Tutup Shift Kasir</h2>
        <p class="text-slate-400 text-sm mt-2">Hitung uang fisik di laci Anda dan masukkan nominalnya di bawah ini.</p>
    </div>
    
    <form action="{{ route('pos.shift.storeClose') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Total Uang Fisik (Rp)</label>
            <input type="number" name="closing_cash_actual" required min="0" placeholder="0" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-lg focus:border-red-500 focus:ring focus:ring-red-500/20 outline-none transition">
            <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Masukkan jumlah uang tunai aktual (Blind Close). Selisih akan dihitung otomatis.</p>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Catatan (Opsional)</label>
            <textarea name="notes" rows="3" placeholder="Alasan selisih uang jika ada..." class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-sm focus:border-red-500 focus:ring focus:ring-red-500/20 outline-none transition"></textarea>
        </div>
        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menutup shift? Anda tidak dapat membuka transaksi lagi di shift ini.')" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-4 rounded-xl shadow-lg transition duration-300">
            <i class="fa-solid fa-lock mr-2"></i> Konfirmasi & Tutup Shift
        </button>
    </form>
</div>
@endsection
EOT;
file_put_contents('resources/views/pos/shift/close.blade.php', $closeShiftView);

$summaryShiftView = <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto mt-8 mb-12" id="printableArea">
    <div class="bg-white text-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200">
        <div class="bg-slate-900 p-8 text-center text-white relative">
            <div class="absolute top-4 right-4 print:hidden">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-white p-3 rounded-lg transition"><i class="fa-solid fa-print"></i></button>
                <a href="{{ route('pos.menu') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white p-3 rounded-lg transition ml-2"><i class="fa-solid fa-house"></i></a>
            </div>
            <i class="fa-solid fa-receipt text-5xl mb-4 text-slate-400"></i>
            <h1 class="text-3xl font-bold uppercase tracking-widest">Shift Summary Report</h1>
            <p class="text-slate-400 mt-2">Sesi ID: #{{ str_pad(\$session->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-2 gap-6 mb-8 border-b border-slate-200 pb-8">
                <div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Kasir Bertugas</div>
                    <div class="text-lg font-bold">{{ \$session->user->name }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Waktu Shift</div>
                    <div class="font-medium">{{ \$session->opening_time->format('d M Y, H:i') }} - {{ \$session->closing_time->format('H:i') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b border-slate-200 pb-2">Penjualan Total</h3>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Total Transaksi</span><span class="font-bold">Rp {{ number_format(\$totalSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">via QRIS / Card</span><span class="font-medium text-blue-600">Rp {{ number_format(\$qrisSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t border-slate-200"><span class="text-slate-800">Pendapatan Tunai</span><span class="text-emerald-600">Rp {{ number_format(\$cashSales, 0, ',', '.') }}</span></div>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b border-slate-200 pb-2">Rekonsiliasi Laci (Cash)</h3>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Modal Awal</span><span class="font-bold">Rp {{ number_format(\$session->opening_cash, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Pendapatan Tunai</span><span class="font-bold">Rp {{ number_format(\$cashSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t border-slate-200"><span class="text-slate-800">Harapan Laci (Sistem)</span><span>Rp {{ number_format(\$session->closing_cash_system, 0, ',', '.') }}</span></div>
                    
                    <div class="flex justify-between mb-2 mt-4"><span class="text-slate-600">Fisik di Laci (Aktual)</span><span class="font-bold text-lg">Rp {{ number_format(\$session->closing_cash_actual, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-2 pt-2 border-t border-slate-200">
                        <span class="text-slate-800">Selisih (Difference)</span>
                        <span class="text-lg {{ \$session->difference == 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format(\$session->difference, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <h3 class="font-bold text-xl mb-4 text-slate-800 border-b-2 border-slate-800 pb-2">Rincian Produk Terjual</h3>
            <table class="w-full text-left mb-8">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300">
                        <th class="p-3 font-bold text-slate-700">Menu</th>
                        <th class="p-3 font-bold text-slate-700 text-center">Qty</th>
                        <th class="p-3 font-bold text-slate-700 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\$productsSold as \$item)
                    <tr class="border-b border-slate-100">
                        <td class="p-3 text-slate-800">{{ \$item->product->name ?? 'Unknown' }}</td>
                        <td class="p-3 text-center text-slate-600 font-bold">{{ \$item->total_qty }}</td>
                        <td class="p-3 text-right text-slate-800 font-bold">{{ number_format(\$item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-slate-500 italic">Tidak ada produk terjual.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="text-center text-sm text-slate-500 mt-12 pt-8 border-t border-slate-200">
                Laporan ini dicetak secara otomatis oleh Sistem Ngafe.
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; margin: 0; padding: 0;}
        body { background: white; }
    }
</style>
@endsection
EOT;
file_put_contents('resources/views/pos/shift/summary.blade.php', $summaryShiftView);

$salesReportView = <<<EOT
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-chart-bar text-blue-400 mr-2"></i> Laporan Penjualan (Sales Report)</h2>
    <form class="flex gap-2">
        <select name="month" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for(\$i=1; \$i<=12; \$i++)
                <option value="{{ sprintf('%02d', \$i) }}" {{ \$month == sprintf('%02d', \$i) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,\$i,10)) }}</option>
            @endfor
        </select>
        <select name="year" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for(\$i=date('Y')-2; \$i<=date('Y'); \$i++)
                <option value="{{ \$i }}" {{ \$year == \$i ? 'selected' : '' }}>{{ \$i }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg transition"><i class="fa-solid fa-filter"></i> Filter</button>
        <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition ml-2 print:hidden"><i class="fa-solid fa-print"></i> Cetak</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 print:hidden">
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Total Item Terjual</div>
        <div class="text-4xl font-bold text-blue-400">{{ \$totalItems }} Porsi</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Total Omset Penjualan</div>
        <div class="text-4xl font-bold text-emerald-400">Rp {{ number_format(\$totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl" id="printableArea">
    <div class="p-6 border-b border-slate-700 hidden print:block text-center text-slate-900">
        <h2 class="text-2xl font-bold mb-2">Laporan Penjualan (Produk Terjual)</h2>
        <p>Periode: {{ date('F Y', mktime(0,0,0,\$month,10,\$year)) }}</p>
    </div>
    
    <div class="p-6 border-b border-slate-700 print:hidden flex justify-between items-center">
        <h3 class="font-bold text-lg text-white">Rincian Produk Terjual ({{ date('F Y', mktime(0,0,0,\$month,10,\$year)) }})</h3>
        <span class="text-sm text-slate-400">Diurutkan berdasarkan produk terlaris</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left print:text-slate-900">
            <thead>
                <tr class="bg-slate-900/50 print:bg-slate-200 text-slate-400 print:text-slate-800 text-sm border-b border-slate-700 print:border-slate-400">
                    <th class="p-4 font-medium">Peringkat</th>
                    <th class="p-4 font-medium">Nama Produk</th>
                    <th class="p-4 font-medium text-center">Kategori</th>
                    <th class="p-4 font-medium text-center">Qty Terjual</th>
                    <th class="p-4 font-medium text-right">Total Omset (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\$productsSold as \$item)
                <tr class="border-b border-slate-700/50 print:border-slate-300 hover:bg-slate-700/20 transition">
                    <td class="p-4 text-slate-300 print:text-slate-800 font-bold">#{{ \$loop->iteration }}</td>
                    <td class="p-4 text-white print:text-slate-900 font-bold">{{ \$item->product->name ?? 'Unknown' }}</td>
                    <td class="p-4 text-center text-sm">
                        <span class="bg-slate-700 print:bg-slate-200 text-slate-300 print:text-slate-800 px-2 py-1 rounded">{{ \$item->product->category->name ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-center font-bold text-blue-400 print:text-blue-800">{{ \$item->total_qty }}</td>
                    <td class="p-4 font-bold text-emerald-400 print:text-emerald-800 text-right">Rp {{ number_format(\$item->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Tidak ada data penjualan di bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if(\$productsSold->count() > 0)
            <tfoot class="bg-slate-900 print:bg-slate-200 border-t border-slate-700 print:border-slate-400">
                <tr>
                    <th colspan="3" class="p-4 text-right text-slate-300 print:text-slate-800 uppercase tracking-wider font-bold">Total Penjualan Keseluruhan</th>
                    <th class="p-4 text-center text-blue-400 print:text-blue-800 font-bold text-lg">{{ \$totalItems }}</th>
                    <th class="p-4 text-right text-emerald-400 print:text-emerald-800 font-bold text-lg">Rp {{ number_format(\$totalRevenue, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; background: white;}
        .bg-slate-800 { background: white !important; }
        .text-white { color: #0f172a !important; }
    }
</style>
@endsection
EOT;
file_put_contents('resources/views/admin/pos/sales_report.blade.php', $salesReportView);

echo "Scripts for POS Shift created.\n";
