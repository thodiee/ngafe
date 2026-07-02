<?php
// 1. Create Migration for Expenses
$migrationName = '2026_05_25_135500_create_expenses_table.php';
$migrationContent = <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expenses', function (Blueprint \$table) {
            \$table->id();
            \$table->date('expense_date');
            \$table->string('category');
            \$table->decimal('amount', 15, 2);
            \$table->string('notes')->nullable();
            \$table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            \$table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('expenses');
    }
};
EOT;
file_put_contents("database/migrations/{$migrationName}", $migrationContent);

// 2. Create Expense Model
$modelContent = <<<EOT
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
    protected \$fillable = ['expense_date', 'category', 'amount', 'notes', 'user_id'];
    public function user() {
        return \$this->belongsTo(User::class);
    }
}
EOT;
file_put_contents('app/Models/Expense.php', $modelContent);

// 3. Create FinanceController
$controllerContent = <<<EOT
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use App\Models\OrderItem;
use App\Models\IngredientBatch;
use Carbon\Carbon;

class FinanceController extends Controller {
    public function dashboard(Request \$request) {
        \$month = \$request->month ?? date('m');
        \$year = \$request->year ?? date('Y');

        \$orders = Order::whereMonth('created_at', \$month)
                       ->whereYear('created_at', \$year)
                       ->where('payment_status', 'paid')
                       ->get();

        \$grossRevenue = \$orders->sum('total_amount');
        \$cogs = 0;

        // Estimated COGS calculation based on items sold
        \$orderItems = OrderItem::whereHas('order', function(\$q) use (\$month, \$year) {
            \$q->whereMonth('created_at', \$month)
              ->whereYear('created_at', \$year)
              ->where('payment_status', 'paid');
        })->with('product.recipes.ingredient')->get();

        foreach (\$orderItems as \$item) {
            if (\$item->product && \$item->product->recipes) {
                foreach (\$item->product->recipes as \$recipe) {
                    \$qtyNeeded = \$recipe->quantity * \$item->quantity;
                    // Get average unit price of ingredient
                    \$avgPrice = IngredientBatch::where('ingredient_id', \$recipe->ingredient_id)->avg('unit_price') ?? 0;
                    \$cogs += (\$qtyNeeded * \$avgPrice);
                }
            }
        }

        \$grossProfit = \$grossRevenue - \$cogs;

        \$expenses = Expense::whereMonth('expense_date', \$month)
                           ->whereYear('expense_date', \$year)
                           ->sum('amount');
                           
        \$netProfit = \$grossProfit - \$expenses;

        // Best selling products
        \$bestSellers = OrderItem::whereHas('order', function(\$q) use (\$month, \$year) {
                            \$q->whereMonth('created_at', \$month)
                              ->whereYear('created_at', \$year)
                              ->where('payment_status', 'paid');
                        })
                        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
                        ->groupBy('product_id')
                        ->orderBy('total_qty', 'desc')
                        ->take(5)
                        ->with('product')
                        ->get();

        return view('admin.finance.dashboard', compact('month', 'year', 'grossRevenue', 'cogs', 'grossProfit', 'expenses', 'netProfit', 'bestSellers'));
    }

    public function expenses(Request \$request) {
        \$month = \$request->month ?? date('m');
        \$year = \$request->year ?? date('Y');
        
        \$expenses = Expense::whereMonth('expense_date', \$month)
                           ->whereYear('expense_date', \$year)
                           ->orderBy('expense_date', 'desc')
                           ->get();
                           
        return view('admin.finance.expenses', compact('expenses', 'month', 'year'));
    }

    public function storeExpense(Request \$request) {
        \$request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        Expense::create([
            'expense_date' => \$request->expense_date,
            'category' => \$request->category,
            'amount' => \$request->amount,
            'notes' => \$request->notes,
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function report(Request \$request) {
        \$month = \$request->month ?? date('m');
        \$year = \$request->year ?? date('Y');

        \$orders = Order::whereMonth('created_at', \$month)
                       ->whereYear('created_at', \$year)
                       ->where('payment_status', 'paid')
                       ->get();

        \$grossRevenue = \$orders->sum('total_amount');
        \$cogs = 0;

        \$orderItems = OrderItem::whereHas('order', function(\$q) use (\$month, \$year) {
            \$q->whereMonth('created_at', \$month)
              ->whereYear('created_at', \$year)
              ->where('payment_status', 'paid');
        })->with('product.recipes')->get();

        foreach (\$orderItems as \$item) {
            if (\$item->product && \$item->product->recipes) {
                foreach (\$item->product->recipes as \$recipe) {
                    \$qtyNeeded = \$recipe->quantity * \$item->quantity;
                    \$avgPrice = IngredientBatch::where('ingredient_id', \$recipe->ingredient_id)->avg('unit_price') ?? 0;
                    \$cogs += (\$qtyNeeded * \$avgPrice);
                }
            }
        }

        \$grossProfit = \$grossRevenue - \$cogs;

        // Group expenses by category
        \$expenseCategories = Expense::whereMonth('expense_date', \$month)
                                    ->whereYear('expense_date', \$year)
                                    ->selectRaw('category, sum(amount) as total')
                                    ->groupBy('category')
                                    ->get();
                                    
        \$totalExpenses = \$expenseCategories->sum('total');
        \$netProfit = \$grossProfit - \$totalExpenses;

        return view('admin.finance.report', compact('month', 'year', 'grossRevenue', 'cogs', 'grossProfit', 'expenseCategories', 'totalExpenses', 'netProfit'));
    }
}
EOT;
file_put_contents('app/Http/Controllers/FinanceController.php', $controllerContent);

// 4. Create Views
@mkdir('resources/views/admin/finance', 0777, true);

$dashboardView = <<<EOT
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-chart-line text-emerald-400 mr-2"></i> Finance Dashboard</h2>
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
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Gross Revenue</div>
        <div class="text-2xl font-bold text-blue-400">Rp {{ number_format(\$grossRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-orange-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Est. COGS (HPP)</div>
        <div class="text-2xl font-bold text-orange-400">Rp {{ number_format(\$cogs, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-purple-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">OPEX (Pengeluaran)</div>
        <div class="text-2xl font-bold text-purple-400">Rp {{ number_format(\$expenses, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-xl relative overflow-hidden">
        <i class="fa-solid fa-coins absolute -right-4 -bottom-4 text-8xl text-emerald-500/10"></i>
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2 relative z-10">Net Profit</div>
        <div class="text-3xl font-bold {{ \$netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} relative z-10">Rp {{ number_format(\$netProfit, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-white"><i class="fa-solid fa-chart-pie text-brand-400 mr-2"></i> Laba Kotor (Gross Margin)</h3>
        <div class="text-4xl font-bold text-white mb-2">Rp {{ number_format(\$grossProfit, 0, ',', '.') }}</div>
        <p class="text-slate-400 text-sm">Pendapatan kotor dikurangi biaya bahan baku. Ini margin kotor sebelum dikurangi operasional seperti gaji dan sewa.</p>
        
        <div class="mt-6">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-300">Margin Persentase</span>
                <span class="font-bold text-white">{{ \$grossRevenue > 0 ? round((\$grossProfit / \$grossRevenue) * 100, 1) : 0 }}%</span>
            </div>
            <div class="w-full bg-slate-700 rounded-full h-2.5">
                <div class="bg-brand-500 h-2.5 rounded-full" style="width: {{ \$grossRevenue > 0 ? round((\$grossProfit / \$grossRevenue) * 100, 1) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-white"><i class="fa-solid fa-fire text-orange-400 mr-2"></i> Produk Paling Laris (Best Sellers)</h3>
        <div class="space-y-4">
            @forelse(\$bestSellers as \$item)
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-300">{{ \$loop->iteration }}</div>
                    <div>
                        <div class="font-bold text-white">{{ \$item->product->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-slate-400">{{ \$item->total_qty }} terjual</div>
                    </div>
                </div>
                <div class="font-bold text-emerald-400">Rp {{ number_format(\$item->total_revenue, 0, ',', '.') }}</div>
            </div>
            @empty
            <div class="text-center text-slate-500 py-4">Belum ada data penjualan bulan ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
EOT;
file_put_contents('resources/views/admin/finance/dashboard.blade.php', $dashboardView);

$expensesView = <<<EOT
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-money-bill-transfer text-purple-400 mr-2"></i> Pengeluaran (OPEX)</h2>
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
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl sticky top-24">
            <h3 class="text-xl font-bold text-white mb-6"><i class="fa-solid fa-plus-circle text-emerald-400 mr-2"></i> Catat Pengeluaran</h3>
            <form action="{{ route('admin.finance.expenses.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Tanggal</label>
                    <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Kategori</label>
                    <select name="category" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                        <option value="Gaji & Upah">Gaji & Upah</option>
                        <option value="Listrik, Air & Internet">Listrik, Air & Internet</option>
                        <option value="Sewa Tempat">Sewa Tempat</option>
                        <option value="Marketing & Promosi">Marketing & Promosi</option>
                        <option value="Pemeliharaan Alat">Pemeliharaan Alat</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Nominal (Rp)</label>
                    <input type="number" name="amount" required min="0" placeholder="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm text-slate-400 mb-1">Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Opsional" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-bold py-3 rounded-lg transition-colors"><i class="fa-solid fa-save mr-2"></i> Simpan Data</button>
            </form>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg text-white">Riwayat Pengeluaran ({{ date('F Y', mktime(0,0,0,\$month,10,\$year)) }})</h3>
                <div class="text-xl font-bold text-red-400">Total: Rp {{ number_format(\$expenses->sum('amount'), 0, ',', '.') }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/50 text-slate-400 text-sm border-b border-slate-700">
                            <th class="p-4 font-medium">Tanggal</th>
                            <th class="p-4 font-medium">Kategori</th>
                            <th class="p-4 font-medium">Keterangan</th>
                            <th class="p-4 font-medium text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\$expenses as \$exp)
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/20 transition">
                            <td class="p-4 text-slate-300">{{ date('d M Y', strtotime(\$exp->expense_date)) }}</td>
                            <td class="p-4">
                                <span class="bg-slate-700 text-xs px-2 py-1 rounded text-slate-300">{{ \$exp->category }}</span>
                            </td>
                            <td class="p-4 text-slate-400 text-sm">{{ \$exp->notes ?: '-' }}</td>
                            <td class="p-4 font-bold text-red-400 text-right">Rp {{ number_format(\$exp->amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">Tidak ada pengeluaran di bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
EOT;
file_put_contents('resources/views/admin/finance/expenses.blade.php', $expensesView);

$reportView = <<<EOT
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-file-invoice-dollar text-blue-400 mr-2"></i> Laporan Laba Rugi</h2>
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
        <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition ml-2"><i class="fa-solid fa-print"></i> Cetak</button>
    </form>
</div>

<div class="bg-white text-slate-900 p-8 md:p-12 rounded-xl shadow-2xl max-w-4xl mx-auto" id="printableArea">
    <div class="text-center mb-8 border-b-2 border-slate-900 pb-6">
        <h1 class="text-3xl font-bold uppercase tracking-widest">Laporan Laba Rugi</h1>
        <p class="text-lg mt-2 font-medium">Periode: {{ date('F Y', mktime(0,0,0,\$month,10,\$year)) }}</p>
    </div>

    <div class="space-y-6">
        <!-- Pendapatan -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Pendapatan (Revenue)</h3>
            <div class="flex justify-between items-center py-2 px-2">
                <span>Total Penjualan (Gross Revenue)</span>
                <span class="font-mono text-lg">Rp {{ number_format(\$grossRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300">
                <span>Total Pendapatan</span>
                <span class="font-mono text-lg">Rp {{ number_format(\$grossRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- HPP -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Harga Pokok Penjualan (COGS)</h3>
            <div class="flex justify-between items-center py-2 px-2">
                <span>Beban Bahan Baku (Estimasi)</span>
                <span class="font-mono text-lg">(Rp {{ number_format(\$cogs, 0, ',', '.') }})</span>
            </div>
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300 text-blue-800">
                <span>Laba Kotor (Gross Profit)</span>
                <span class="font-mono text-lg">Rp {{ number_format(\$grossProfit, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Beban Operasional -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Biaya Operasional (OPEX)</h3>
            @forelse(\$expenseCategories as \$cat)
            <div class="flex justify-between items-center py-2 px-2 border-b border-slate-100 last:border-0">
                <span>Beban {{ \$cat->category }}</span>
                <span class="font-mono text-lg">(Rp {{ number_format(\$cat->total, 0, ',', '.') }})</span>
            </div>
            @empty
            <div class="py-2 px-2 text-slate-500 italic">Tidak ada pengeluaran tercatat.</div>
            @endforelse
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300 text-red-800">
                <span>Total Biaya Operasional</span>
                <span class="font-mono text-lg">(Rp {{ number_format(\$totalExpenses, 0, ',', '.') }})</span>
            </div>
        </div>

        <!-- Laba Bersih -->
        <div class="mt-8 pt-4 border-t-4 border-slate-900">
            <div class="flex justify-between items-center py-4 px-2 bg-slate-900 text-white rounded-lg">
                <span class="font-bold text-xl uppercase tracking-wider ml-2">Laba Bersih (Net Profit)</span>
                <span class="font-mono text-2xl font-bold mr-2">Rp {{ number_format(\$netProfit, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    <div class="mt-16 pt-8 border-t border-slate-300 flex justify-between text-center text-sm">
        <div class="w-48">
            <p class="mb-16">Disetujui Oleh,</p>
            <div class="border-t border-slate-900 pt-2 font-bold">Direktur / Owner</div>
        </div>
        <div class="w-48">
            <p class="mb-16">Dibuat Oleh,</p>
            <div class="border-t border-slate-900 pt-2 font-bold">Finance / Admin</div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
        .text-white { color: #0f172a !important; }
        .bg-slate-900 { background-color: #f1f5f9 !important; color: #0f172a !important; border: 2px solid #0f172a; }
    }
</style>
@endsection
EOT;
file_put_contents('resources/views/admin/finance/report.blade.php', $reportView);

echo "Finance components created.\n";
