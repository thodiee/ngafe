<?php

$viewsDir = __DIR__ . '/resources/views';

$dirs = ['auth', 'admin'];
foreach ($dirs as $d) {
    if (!is_dir($viewsDir . '/' . $d)) {
        mkdir($viewsDir . '/' . $d, 0777, true);
    }
}

$views = [
    'auth/login.blade.php' => <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="glass-dark p-8 rounded-3xl w-full max-w-md shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-400 to-blue-500"></div>
        <h2 class="text-3xl font-bold mb-6 text-white text-center"><i class="fa-solid fa-mug-hot text-brand-500 mr-2"></i> Ngafe Login</h2>
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white focus:border-brand-500 outline-none transition" required>
            </div>
            <div class="mb-8">
                <label class="block text-sm text-slate-400 mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white focus:border-brand-500 outline-none transition" required>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95 text-lg">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Sign In
            </button>
        </form>
    </div>
</div>
@endsection
BLADE,
    'admin/layout.blade.php' => <<<'BLADE'
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ngafe Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 antialiased min-h-screen flex">
    
    <!-- Sidebar -->
    <div class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col fixed h-full">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-mug-hot text-emerald-500 mr-2"></i> Ngafe Admin</h1>
        </div>
        <nav class="flex-grow px-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-chart-line w-6"></i> Dashboard</a>
            <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-users w-6"></i> Pegawai</a>
            <a href="{{ route('admin.categories.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-tags w-6"></i> Kategori Menu</a>
            <a href="{{ route('admin.products.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-burger w-6"></i> Menu Produk</a>
            <a href="{{ route('admin.ingredients.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.ingredients.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-boxes-stacked w-6"></i> Inventaris</a>
            <a href="{{ route('admin.attendances.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.attendances.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-300' }}"><i class="fa-solid fa-clipboard-user w-6"></i> Absensi</a>
            <a href="{{ route('pos.menu') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-700 transition text-slate-300 mt-4 border-t border-slate-700 pt-4"><i class="fa-solid fa-cash-register w-6"></i> Go to POS</a>
        </nav>
        <div class="p-4 border-t border-slate-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-red-500/20 rounded-lg transition"><i class="fa-solid fa-right-from-bracket w-6"></i> Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow ml-64 p-8">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/20 border border-green-500 text-green-200">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500 text-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
BLADE,
    'admin/dashboard.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<h2 class="text-3xl font-bold mb-8">Admin Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-lg">
        <div class="text-slate-400 text-sm mb-2">Total Pegawai</div>
        <div class="text-3xl font-bold text-white">{{ $userCount }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-lg">
        <div class="text-slate-400 text-sm mb-2">Total Menu</div>
        <div class="text-3xl font-bold text-white">{{ $productCount }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-purple-500 shadow-lg">
        <div class="text-slate-400 text-sm mb-2">Absensi Hari Ini</div>
        <div class="text-3xl font-bold text-white">{{ $attendanceCount }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-red-500 shadow-lg">
        <div class="text-slate-400 text-sm mb-2">Stok Tipis</div>
        <div class="text-3xl font-bold text-red-400">{{ $lowStock }}</div>
    </div>
</div>
@endsection
BLADE,
    'admin/users.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Pegawai / Users</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Pegawai</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama</th>
                <th class="p-4">Email / Username</th>
                <th class="p-4">Role</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $user->name }}</td>
                <td class="p-4">{{ $user->email }}</td>
                <td class="p-4"><span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-bold uppercase">{{ $user->role }}</span></td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Pegawai</h3>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Email (Sebagai Username)</label>
                <input type="email" name="email" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Role</label>
                <select name="role" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="cashier">Cashier</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Password</label>
                <input type="password" name="password" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE,
    'admin/categories.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Kategori Menu</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Kategori</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">ID</th>
                <th class="p-4">Nama Kategori</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ $cat->id }}</td>
                <td class="p-4 font-bold">{{ $cat->name }}</td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Menghapus kategori akan menghapus produk yang terhubung! Yakin?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Kategori</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nama Kategori</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE,
    'admin/products.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Menu / Produk</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Menu</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Menu</th>
                <th class="p-4">Kategori</th>
                <th class="p-4">Harga</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $prod->name }}</td>
                <td class="p-4"><span class="bg-purple-500/20 text-purple-400 px-2 py-1 rounded text-xs font-bold">{{ $prod->category->name ?? 'Uncategorized' }}</span></td>
                <td class="p-4">Rp{{ number_format($prod->price, 0, ',', '.') }}</td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Menu</h3>
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Menu</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Kategori</label>
                <select name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Harga (Rp)</label>
                <input type="number" name="price" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE,
    'admin/ingredients.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Inventaris (Bahan Baku)</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Bahan</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Bahan</th>
                <th class="p-4">Stok Saat Ini</th>
                <th class="p-4">Minimal Stok</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingredients as $ing)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $ing->name }}</td>
                <td class="p-4">
                    <span class="{{ $ing->stock <= $ing->minimum_stock ? 'text-red-400 font-bold' : 'text-emerald-400' }}">{{ $ing->stock }}</span> {{ $ing->unit }}
                </td>
                <td class="p-4">{{ $ing->minimum_stock }} {{ $ing->unit }}</td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bahan baku ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Bahan Baku</h3>
        <form action="{{ route('admin.ingredients.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Bahan</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Stok Awal</label>
                    <input type="number" name="stock" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Satuan (Unit)</label>
                    <input type="text" name="unit" placeholder="gram/ml/pcs" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Batas Minimal Stok (Notifikasi)</label>
                <input type="number" name="minimum_stock" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE,
    'admin/attendances.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Riwayat Absensi</h2>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Tanggal</th>
                <th class="p-4">Pegawai</th>
                <th class="p-4">Clock In</th>
                <th class="p-4">Clock Out</th>
                <th class="p-4">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $att)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ $att->date }}</td>
                <td class="p-4 font-bold">{{ $att->user->name ?? 'Unknown' }}</td>
                <td class="p-4 text-emerald-400 font-mono">{{ $att->clock_in ?? '-' }}</td>
                <td class="p-4 text-orange-400 font-mono">{{ $att->clock_out ?? '-' }}</td>
                <td class="p-4">
                    @if($att->status == 'ontime')
                        <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-bold uppercase">On Time</span>
                    @else
                        <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs font-bold uppercase">{{ $att->status }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
BLADE
];

foreach ($views as $name => $content) {
    $path = $viewsDir . '/' . $name;
    file_put_contents($path, $content);
    echo "Created $name\n";
}

echo "Admin views created.\n";
