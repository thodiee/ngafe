<?php
$controllerCode = <<<EOT
<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function index() {
        // Get all orders with status 'processing' (which means paid/sent to kitchen)
        \$orders = Order::where('order_status', 'processing')
                       ->with('table', 'items.product', 'items.variants')
                       ->orderBy('created_at', 'asc')
                       ->get();
                       
        return view('kitchen.index', compact('orders'));
    }

    public function markAsReady(Order \$order) {
        if (\$order->order_status == 'processing') {
            \$order->update(['order_status' => 'ready']);
        }
        return redirect()->route('kitchen.index')->with('success', 'Pesanan ' . \$order->order_reference . ' ditandai selesai.');
    }
}
EOT;

$viewCode = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - NGAFE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="30"> <!-- Auto refresh every 30 seconds -->
</head>
<body class="bg-slate-900 text-slate-300 font-sans min-h-screen">
    
    <!-- Header -->
    <header class="bg-slate-800 border-b border-slate-700 p-4 sticky top-0 z-50 flex justify-between items-center shadow-lg">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-fire-burner text-orange-500 mr-2"></i> Layar Dapur (Kitchen Display)</h1>
            <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-bold animate-pulse">Auto-refresh 30s</span>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Admin</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-400 px-4 py-3 rounded-lg mb-6 shadow-lg">
                <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(\$orders->count() == 0)
            <div class="flex flex-col items-center justify-center py-20 text-slate-500">
                <i class="fa-solid fa-mug-hot text-6xl mb-4 opacity-50"></i>
                <p class="text-xl font-bold">Tidak ada antrean pesanan.</p>
                <p class="text-sm">Dapur bisa beristirahat sejenak.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach(\$orders as \$order)
                <div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl overflow-hidden flex flex-col">
                    <!-- Card Header -->
                    <div class="bg-slate-700/50 p-4 border-b border-slate-600 flex justify-between items-start">
                        <div>
                            <div class="text-xs text-slate-400 font-mono">{{ \$order->order_reference }}</div>
                            <div class="text-xl font-bold text-white mt-1">
                                {{ \$order->order_type == 'dine_in' ? 'Meja ' . (\$order->table->table_number ?? '-') : 'Takeaway' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400">{{ \$order->created_at->diffForHumans() }}</div>
                            <div class="text-xs font-bold px-2 py-1 bg-blue-500/20 text-blue-400 rounded mt-1 uppercase">{{ str_replace('_', ' ', \$order->order_type) }}</div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="p-4 flex-grow">
                        <ul class="space-y-4">
                            @foreach(\$order->items as \$item)
                            <li class="border-b border-slate-700 pb-3 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-white text-lg">
                                        <span class="text-orange-400 mr-2">{{ \$item->quantity }}x</span>
                                        {{ \$item->product->name ?? 'Produk Dihapus' }}
                                    </div>
                                </div>
                                @if(\$item->variants->count() > 0)
                                    <div class="mt-1 pl-6 text-sm text-slate-400">
                                        <ul class="list-disc list-inside">
                                            @foreach(\$item->variants as \$variant)
                                                <li>{{ \$variant->variant_name }}: <span class="text-white">{{ \$variant->option_name }}</span></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(\$item->notes)
                                    <div class="mt-2 pl-6 text-sm text-amber-400 italic">
                                        <i class="fa-solid fa-comment-dots mr-1"></i> "{{ \$item->notes }}"
                                    </div>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Card Footer / Action -->
                    <div class="p-4 bg-slate-900 border-t border-slate-700">
                        <form action="{{ route('kitchen.ready', \$order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg shadow-lg transition-colors flex items-center justify-center text-lg">
                                <i class="fa-solid fa-check-double mr-2"></i> SELESAI
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>
EOT;

file_put_contents('app/Http/Controllers/KitchenController.php', $controllerCode);
@mkdir('resources/views/kitchen', 0777, true);
file_put_contents('resources/views/kitchen/index.blade.php', $viewCode);

echo "Kitchen components created.\n";
