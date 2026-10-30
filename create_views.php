<?php

$viewsDir = __DIR__ . '/resources/views';

// Create directories if they don't exist
$dirs = ['layouts', 'pos', 'hr', 'inventory', 'careers'];
foreach ($dirs as $d) {
    if (!is_dir($viewsDir . '/' . $d)) {
        mkdir($viewsDir . '/' . $d, 0777, true);
    }
}

$views = [
    'layouts/app.blade.php' => <<<'BLADE'
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngafe - F&B Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 antialiased min-h-screen flex flex-col">
    <nav class="glass-dark sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-2 text-xl font-bold text-brand-500">
                        <i class="fa-solid fa-mug-hot"></i> Ngafe
                    </a>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('pos.pinpad') }}" class="text-slate-300 hover:text-white transition"><i class="fa-solid fa-cash-register mr-1"></i> POS</a>
                    <a href="{{ route('hr.dashboard') }}" class="text-slate-300 hover:text-white transition"><i class="fa-solid fa-users mr-1"></i> HR</a>
                    <a href="{{ route('inventory.index') }}" class="text-slate-300 hover:text-white transition"><i class="fa-solid fa-boxes-stacked mr-1"></i> Inventory</a>
                    <a href="{{ route('careers.jobs') }}" class="text-slate-300 hover:text-white transition"><i class="fa-solid fa-briefcase mr-1"></i> Careers</a>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="flex-grow p-4 md:p-8">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/20 border border-green-500 text-green-200">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500 text-red-200">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
    
    <footer class="glass-dark mt-auto py-6 text-center text-slate-400 text-sm">
        <p>&copy; {{ date('Y') }} Ngafe Management System. Designed for scale.</p>
    </footer>
</body>
</html>
BLADE,
    'welcome.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] text-center">
    <div class="inline-block p-4 rounded-full bg-brand-500/20 text-brand-500 mb-6 animate-pulse">
        <i class="fa-solid fa-mug-hot text-6xl"></i>
    </div>
    <h1 class="text-5xl md:text-7xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-400 to-blue-500 mb-4">
        Welcome to Ngafe
    </h1>
    <p class="text-xl text-slate-400 max-w-2xl mb-10">
        The complete Food & Beverage Management System integrating POS, Human Resources, Inventory BOM, and Recruitment into a single elegant platform.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full max-w-6xl">
        <a href="{{ route('pos.pinpad') }}" class="glass-dark p-6 rounded-2xl hover:scale-105 hover:bg-slate-800 transition duration-300 border-t-4 border-t-blue-500">
            <i class="fa-solid fa-cash-register text-3xl text-blue-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-2">POS System</h2>
            <p class="text-slate-400 text-sm">Fast & reliable point of sale with PIN security.</p>
        </a>
        <a href="{{ route('hr.dashboard') }}" class="glass-dark p-6 rounded-2xl hover:scale-105 hover:bg-slate-800 transition duration-300 border-t-4 border-t-purple-500">
            <i class="fa-solid fa-users text-3xl text-purple-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-2">HR Management</h2>
            <p class="text-slate-400 text-sm">Manage attendance, shifts, and schedules seamlessly.</p>
        </a>
        <a href="{{ route('inventory.index') }}" class="glass-dark p-6 rounded-2xl hover:scale-105 hover:bg-slate-800 transition duration-300 border-t-4 border-t-emerald-500">
            <i class="fa-solid fa-boxes-stacked text-3xl text-emerald-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-2">Inventory BOM</h2>
            <p class="text-slate-400 text-sm">Track raw ingredients and recipe compositions.</p>
        </a>
        <a href="{{ route('careers.jobs') }}" class="glass-dark p-6 rounded-2xl hover:scale-105 hover:bg-slate-800 transition duration-300 border-t-4 border-t-orange-500">
            <i class="fa-solid fa-briefcase text-3xl text-orange-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-2">ATS Careers</h2>
            <p class="text-slate-400 text-sm">Publish job vacancies and track applications.</p>
        </a>
    </div>
</div>
@endsection
BLADE,
    'pos/pinpad.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass-dark p-8 rounded-3xl w-full max-w-sm shadow-2xl text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-400 to-blue-500"></div>
        <h2 class="text-2xl font-bold mb-6 text-white"><i class="fa-solid fa-lock text-brand-500 mr-2"></i> Enter POS PIN</h2>
        
        <form action="{{ route('pos.verify') }}" method="POST" id="pinForm">
            @csrf
            <input type="password" name="pin" id="pinDisplay" class="w-full text-center text-3xl tracking-[1em] bg-slate-900 border border-slate-700 rounded-xl py-4 mb-8 text-white focus:outline-none focus:border-brand-500 transition" readonly>
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                @for($i=1; $i<=9; $i++)
                    <button type="button" onclick="addPin('{{$i}}')" class="h-16 rounded-full bg-slate-800 hover:bg-brand-600 transition text-2xl font-semibold text-white shadow-lg active:scale-95">{{ $i }}</button>
                @endfor
                <button type="button" onclick="clearPin()" class="h-16 rounded-full bg-red-900/50 text-red-400 hover:bg-red-600 hover:text-white transition text-xl font-semibold shadow-lg active:scale-95"><i class="fa-solid fa-eraser"></i></button>
                <button type="button" onclick="addPin('0')" class="h-16 rounded-full bg-slate-800 hover:bg-brand-600 transition text-2xl font-semibold text-white shadow-lg active:scale-95">0</button>
                <button type="submit" class="h-16 rounded-full bg-brand-600 hover:bg-brand-500 transition text-xl font-semibold text-white shadow-lg active:scale-95"><i class="fa-solid fa-check"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    function addPin(num) {
        const display = document.getElementById('pinDisplay');
        if (display.value.length < 6) {
            display.value += num;
        }
    }
    function clearPin() {
        document.getElementById('pinDisplay').value = '';
    }
</script>
@endsection
BLADE,
    'pos/menu.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex gap-6 h-[80vh]">
    <!-- Menu Section -->
    <div class="w-2/3 flex flex-col">
        <h2 class="text-3xl font-bold mb-6 text-white"><i class="fa-solid fa-utensils text-brand-500 mr-2"></i> POS Menu</h2>
        <div class="grid grid-cols-3 gap-4 overflow-y-auto pr-2 pb-10">
            @foreach($products as $product)
            <div class="glass-dark p-4 rounded-2xl cursor-pointer hover:border-brand-500 hover:-translate-y-1 transition duration-300 group" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                <div class="aspect-video bg-slate-800 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    @else
                        <i class="fa-solid fa-burger text-4xl text-slate-600"></i>
                    @endif
                </div>
                <h3 class="font-semibold text-lg text-white mb-1">{{ $product->name }}</h3>
                <p class="text-brand-400 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="w-1/3 glass-dark p-6 rounded-3xl flex flex-col border-t-4 border-t-brand-500 shadow-2xl relative">
        <h3 class="text-xl font-bold mb-4 border-b border-slate-700 pb-2"><i class="fa-solid fa-cart-shopping mr-2"></i> Current Order</h3>
        
        <form action="{{ route('pos.order') }}" method="POST" id="orderForm" class="flex flex-col h-full">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-1">Select Table</label>
                <select name="table_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-white" required>
                    <option value="">-- Choose Table --</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">Table {{ $table->table_number }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-1">Order Type</label>
                <div class="flex gap-2">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="dine_in" class="peer hidden" checked>
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-800 p-2 rounded-lg transition"><i class="fa-solid fa-bell-concierge mr-1"></i> Dine In</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="takeaway" class="peer hidden">
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-800 p-2 rounded-lg transition"><i class="fa-solid fa-bag-shopping mr-1"></i> Takeaway</div>
                    </label>
                </div>
            </div>
            
            <div id="cartItems" class="flex-grow overflow-y-auto mb-4 space-y-2 border-t border-slate-700 pt-4">
                <div class="text-center text-slate-500 mt-10" id="emptyCart">Cart is empty</div>
            </div>
            
            <div class="border-t border-slate-700 pt-4 mb-4">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total</span>
                    <span id="cartTotal" class="text-brand-400">Rp 0</span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-white" required>
                    <option value="cash">Cash</option>
                    <option value="qris_gateway">QRIS Gateway</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 text-white font-bold py-4 rounded-xl shadow-lg transition active:scale-95 text-lg" id="checkoutBtn" disabled>
                Process Order
            </button>
        </form>
    </div>
</div>

<script>
    let cart = {};
    
    function addToCart(id, name, price) {
        if(cart[id]) {
            cart[id].quantity++;
        } else {
            cart[id] = { id, name, price, quantity: 1 };
        }
        updateCartUI();
    }
    
    function removeFromCart(id) {
        if(cart[id]) {
            cart[id].quantity--;
            if(cart[id].quantity <= 0) delete cart[id];
        }
        updateCartUI();
    }
    
    function updateCartUI() {
        const container = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const checkoutBtn = document.getElementById('checkoutBtn');
        let total = 0;
        let html = '';
        let index = 0;
        
        for(let id in cart) {
            let item = cart[id];
            total += item.price * item.quantity;
            html += `
                <div class="flex justify-between items-center bg-slate-800 p-3 rounded-lg animate-[fadeIn_0.3s]">
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    <div class="flex-1">
                        <div class="font-semibold text-sm">${item.name}</div>
                        <div class="text-brand-400 text-xs">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="removeFromCart(${item.id})" class="text-slate-400 hover:text-white bg-slate-700 rounded-full w-6 h-6 flex items-center justify-center">-</button>
                        <span class="font-bold w-4 text-center">${item.quantity}</span>
                        <button type="button" onclick="addToCart(${item.id}, '${item.name}', ${item.price})" class="text-brand-500 hover:text-white bg-slate-700 rounded-full w-6 h-6 flex items-center justify-center">+</button>
                    </div>
                </div>
            `;
            index++;
        }
        
        if(index > 0) {
            container.innerHTML = html;
            checkoutBtn.disabled = false;
        } else {
            container.innerHTML = '<div class="text-center text-slate-500 mt-10" id="emptyCart">Cart is empty</div>';
            checkoutBtn.disabled = true;
        }
        
        document.getElementById('cartTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
BLADE,
    'pos/receipt.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex justify-center items-center py-10">
    <div class="bg-white text-slate-900 p-8 w-full max-w-sm rounded-lg shadow-2xl relative">
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -bottom-4 -left-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -bottom-4 -right-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><i class="fa-solid fa-mug-hot"></i> NGAFE</h2>
            <p class="text-sm text-slate-500">Jl. Suka-Suka No. 1, Jakarta</p>
            <p class="text-sm text-slate-500">Telp: 021-1234567</p>
        </div>
        
        <div class="border-t-2 border-dashed border-slate-300 py-4 mb-4">
            <div class="flex justify-between text-sm">
                <span>Order Ref:</span>
                <span class="font-semibold">{{ $order->order_reference }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Type:</span>
                <span class="uppercase font-semibold">{{ str_replace('_', ' ', $order->order_type) }}</span>
            </div>
        </div>
        
        <div class="border-t-2 border-dashed border-slate-300 py-4 mb-4">
            <table class="w-full text-sm">
                @foreach($order->items as $item)
                <tr>
                    <td class="py-1">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="py-1 text-right">{{ $item->quantity }}x</td>
                    <td class="py-1 text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        
        <div class="border-t-2 border-slate-800 pt-4">
            <div class="flex justify-between text-lg font-bold">
                <span>TOTAL</span>
                <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm mt-2">
                <span>Payment</span>
                <span class="uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Status</span>
                <span class="uppercase font-bold {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">{{ $order->payment_status }}</span>
            </div>
        </div>
        
        @if($order->payment_method == 'qris_gateway' && $order->payment_status == 'unpaid')
            <div class="mt-8 text-center">
                <p class="font-bold mb-2">Scan to Pay (Dummy QR)</p>
                <div class="bg-slate-200 w-48 h-48 mx-auto flex items-center justify-center rounded-lg border-4 border-slate-300">
                    <i class="fa-solid fa-qrcode text-6xl text-slate-800"></i>
                </div>
            </div>
        @endif
        
        <div class="text-center mt-10 text-sm italic text-slate-500">
            Terima kasih atas kunjungan Anda!
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('pos.menu') }}" class="inline-block bg-slate-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-slate-800 transition">Back to Menu</a>
        </div>
    </div>
</div>
@endsection
BLADE,
    'hr/dashboard.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-500">HR Dashboard</h1>
            <p class="text-slate-400">Employee Attendance & Scheduling</p>
        </div>
        <div class="flex gap-4">
            <form action="{{ route('hr.clockIn') }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 font-bold"><i class="fa-solid fa-right-to-bracket"></i> Clock In</button>
            </form>
            <form action="{{ route('hr.clockOut') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 font-bold"><i class="fa-solid fa-right-from-bracket"></i> Clock Out</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Attendance Table -->
        <div class="glass-dark rounded-2xl overflow-hidden border border-slate-700 shadow-xl">
            <div class="bg-slate-800 px-6 py-4 border-b border-slate-700">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-clipboard-user mr-2 text-purple-400"></i> Today's Attendance</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-400 text-sm border-b border-slate-700">
                                <th class="pb-3">Employee</th>
                                <th class="pb-3">Time In</th>
                                <th class="pb-3">Time Out</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition">
                                <td class="py-4">{{ $att->user->name ?? 'User '.$att->user_id }}</td>
                                <td class="py-4 font-mono text-brand-400">{{ $att->clock_in }}</td>
                                <td class="py-4 font-mono text-brand-400">{{ $att->clock_out ?? '-' }}</td>
                                <td class="py-4">
                                    @if($att->status == 'ontime')
                                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-bold border border-green-500/30">On Time</span>
                                    @else
                                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold border border-yellow-500/30">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">No attendance records yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="glass-dark rounded-2xl overflow-hidden border border-slate-700 shadow-xl">
            <div class="bg-slate-800 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-calendar-days mr-2 text-pink-400"></i> Upcoming Schedules</h3>
                <button class="text-sm bg-slate-700 hover:bg-slate-600 px-3 py-1 rounded-lg transition text-slate-300">Add Schedule</button>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($schedules as $sched)
                    <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-xl border border-slate-700 hover:border-pink-500/50 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-pink-500/20 rounded-full flex items-center justify-center text-pink-400 font-bold">
                                {{ substr($sched->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-white">{{ $sched->user->name ?? 'User '.$sched->user_id }}</h4>
                                <p class="text-sm text-slate-400">{{ $sched->date }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-brand-400">{{ $sched->shift->name ?? 'Shift' }}</div>
                            <div class="text-xs text-slate-500">{{ $sched->shift->start_time ?? '' }} - {{ $sched->shift->end_time ?? '' }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">No schedules set</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE,
    'inventory/index.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-500">Inventory & BOM</h1>
            <p class="text-slate-400">Raw Materials & Bill of Materials</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ingredients Stock -->
        <div class="glass-dark rounded-2xl overflow-hidden border border-slate-700 shadow-xl">
            <div class="bg-slate-800 px-6 py-4 border-b border-slate-700">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-boxes-stacked mr-2 text-emerald-400"></i> Ingredients Stock</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-400 text-sm border-b border-slate-700">
                                <th class="pb-3">Ingredient Name</th>
                                <th class="pb-3">Current Stock</th>
                                <th class="pb-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingredients as $ing)
                            <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition">
                                <td class="py-4">
                                    <div class="font-bold">{{ $ing->name }}</div>
                                    @if($ing->stock <= $ing->minimum_stock)
                                        <span class="text-xs text-red-400"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>
                                    @endif
                                </td>
                                <td class="py-4 font-mono">
                                    <span class="text-lg {{ $ing->stock <= $ing->minimum_stock ? 'text-red-400 font-bold' : 'text-emerald-400' }}">{{ $ing->stock }}</span> 
                                    <span class="text-slate-500 text-sm">{{ $ing->unit }}</span>
                                </td>
                                <td class="py-4 text-right">
                                    <form action="{{ route('inventory.update') }}" method="POST" class="flex gap-2 justify-end">
                                        @csrf
                                        <input type="hidden" name="ingredient_id" value="{{ $ing->id }}">
                                        <input type="number" name="stock" value="{{ $ing->stock }}" class="w-20 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-sm text-white focus:border-emerald-500 outline-none">
                                        <button type="submit" class="bg-slate-700 hover:bg-emerald-600 text-white px-3 py-1 rounded text-sm transition"><i class="fa-solid fa-rotate"></i></button>
                                    </form>
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
        <div class="glass-dark rounded-2xl overflow-hidden border border-slate-700 shadow-xl">
            <div class="bg-slate-800 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-list-check mr-2 text-teal-400"></i> Bill of Materials (BOM)</h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @forelse($products as $prod)
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
                        <h4 class="font-bold text-xl text-white mb-3 flex justify-between items-center">
                            {{ $prod->name }}
                            <span class="text-sm font-normal text-slate-400">{{ $prod->category }}</span>
                        </h4>
                        @if($prod->recipes->count() > 0)
                            <ul class="space-y-2">
                                @foreach($prod->recipes as $recipe)
                                <li class="flex justify-between items-center text-sm border-b border-slate-700/50 pb-2">
                                    <span class="text-slate-300"><i class="fa-solid fa-caret-right text-teal-500 mr-2"></i>{{ $recipe->ingredient->name ?? 'Unknown' }}</span>
                                    <span class="font-mono text-teal-400 bg-teal-900/30 px-2 py-0.5 rounded">{{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? '' }}</span>
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
BLADE,
    'careers/jobs.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <div class="inline-block p-4 rounded-full bg-orange-500/20 text-orange-500 mb-4">
            <i class="fa-solid fa-briefcase text-4xl"></i>
        </div>
        <h1 class="text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-orange-400 to-amber-500">Join Our Team</h1>
        <p class="text-slate-400 text-lg">Build your career with Ngafe. We are always looking for passionate individuals.</p>
    </div>

    <div class="space-y-6 mb-12">
        @forelse($jobs as $job)
        <div class="glass-dark rounded-2xl border border-slate-700 shadow-xl p-6 md:p-8 hover:border-orange-500/50 transition duration-300 group">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2 group-hover:text-orange-400 transition">{{ $job->title }}</h2>
                    <span class="bg-slate-800 text-slate-300 px-3 py-1 rounded-full text-sm border border-slate-600"><i class="fa-solid fa-location-dot mr-1"></i> Full Time / On-site</span>
                </div>
                <button onclick="openApplyModal({{ $job->id }}, '{{ addslashes($job->title) }}')" class="bg-orange-600 hover:bg-orange-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shrink-0">
                    Apply Now <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>
            
            <div class="prose prose-invert max-w-none text-slate-300">
                <div class="mb-4">
                    <h3 class="text-white text-lg font-semibold mb-2">Description:</h3>
                    <p>{{ $job->description }}</p>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-2">Requirements:</h3>
                    <p class="whitespace-pre-line">{{ $job->requirements }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-dark rounded-2xl p-12 text-center text-slate-400 border border-slate-700">
            <i class="fa-solid fa-inbox text-5xl mb-4 text-slate-600"></i>
            <h3 class="text-xl font-bold text-white mb-2">No Open Vacancies</h3>
            <p>We are not actively hiring right now, but please check back later.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal -->
<div id="applyModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden animate-[fadeIn_0.2s]">
        <div class="bg-slate-900 px-6 py-4 flex justify-between items-center border-b border-slate-700">
            <h3 class="font-bold text-xl text-white">Apply for <span id="modalJobTitle" class="text-orange-400">Role</span></h3>
            <button onclick="closeApplyModal()" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="job_vacancy_id" id="modalJobId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="applicant_name" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-orange-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-orange-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-orange-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Resume / CV (PDF only, max 2MB)</label>
                    <input type="file" name="cv" accept=".pdf" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-orange-500 outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-500/20 file:text-orange-400 hover:file:bg-orange-500/30">
                </div>
            </div>
            
            <div class="mt-8">
                <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApplyModal(id, title) {
        document.getElementById('modalJobId').value = id;
        document.getElementById('modalJobTitle').innerText = title;
        document.getElementById('applyModal').classList.remove('hidden');
    }
    function closeApplyModal() {
        document.getElementById('applyModal').classList.add('hidden');
    }
</script>
@endsection
BLADE
];

foreach ($views as $name => $content) {
    $path = $viewsDir . '/' . $name;
    file_put_contents($path, $content);
    echo "Created $name\n";
}

echo "All views created.\n";
