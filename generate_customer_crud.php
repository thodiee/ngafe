<?php
$controllerCode = <<<EOT
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index() {
        \$data = Customer::orderBy('name')->get();
        return view('admin.customers.index', compact('data'));
    }

    public function create() {
        return view('admin.customers.form');
    }

    public function store(Request \$request) {
        \$request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:customers,phone',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);
        Customer::create(\$request->all());
        return redirect()->route('admin.customers.index')->with('success', 'Customer saved');
    }

    public function edit(Customer \$customer) {
        \$item = \$customer;
        return view('admin.customers.form', compact('item'));
    }

    public function update(Request \$request, Customer \$customer) {
        \$request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:customers,phone,' . \$customer->id,
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);
        \$customer->update(\$request->all());
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated');
    }

    public function destroy(Customer \$customer) {
        \$customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted');
    }
}
EOT;

$indexCode = <<<EOT
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Data Pelanggan (Member)</h2>
    <a href="{{ route('admin.customers.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold transition"><i class="fa-solid fa-plus mr-2"></i> Tambah Pelanggan</a>
</div>
<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama</th>
                <th class="p-4">No HP</th>
                <th class="p-4">Alamat</th>
                <th class="p-4">Tanggal Lahir</th>
                <th class="p-4">Total Poin</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse(\$data as \$item)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold text-white">{{ \$item->name }}</td>
                <td class="p-4">{{ \$item->phone }}</td>
                <td class="p-4 text-sm">{{ \$item->address ?? '-' }}</td>
                <td class="p-4 text-sm">{{ \$item->dob ?? '-' }}</td>
                <td class="p-4 font-bold text-emerald-400">{{ number_format(\$item->points) }}</td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.customers.edit', \$item) }}" class="text-blue-400 hover:text-blue-300"><i class="fa-solid fa-pen-to-square"></i></a>
                    <form action="{{ route('admin.customers.destroy', \$item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-slate-400">Belum ada pelanggan terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
EOT;

$formCode = <<<EOT
@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold">{{ isset(\$item) ? 'Edit' : 'Tambah' }} Pelanggan</h2>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset(\$item) ? route('admin.customers.update', \$item) : route('admin.customers.store') }}" method="POST">
        @csrf
        @if(isset(\$item)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', \$item->name ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Nomor Telepon (Wajib, Unik)</label>
            <input type="text" name="phone" value="{{ old('phone', \$item->phone ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Alamat (Opsional)</label>
            <textarea name="address" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500">{{ old('address', \$item->address ?? '') }}</textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Tanggal Lahir (Opsional)</label>
            <input type="date" name="dob" value="{{ old('dob', \$item->dob ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500">
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition font-bold">Simpan</button>
            <a href="{{ route('admin.customers.index') }}" class="ml-4 text-slate-400 hover:text-white">Batal</a>
        </div>
    </form>
</div>
@endsection
EOT;

file_put_contents('app/Http/Controllers/Admin/CustomerController.php', $controllerCode);
@mkdir('resources/views/admin/customers', 0777, true);
file_put_contents('resources/views/admin/customers/index.blade.php', $indexCode);
file_put_contents('resources/views/admin/customers/form.blade.php', $formCode);

echo "Customer CRUD created.\n";
