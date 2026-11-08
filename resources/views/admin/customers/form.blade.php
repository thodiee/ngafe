@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} Pelanggan</h2>
</div>
<div class="bg-slate-800 rounded-xl p-4 sm:p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.customers.update', $item) : route('admin.customers.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Nomor Telepon (Wajib, Unik)</label>
            <input type="text" name="phone" value="{{ old('phone', $item->phone ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Alamat (Opsional)</label>
            <textarea name="address" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500">{{ old('address', $item->address ?? '') }}</textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Tanggal Lahir (Opsional)</label>
            <input type="date" name="dob" value="{{ old('dob', $item->dob ?? '') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500">
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg transition font-bold">Simpan</button>
            <a href="{{ route('admin.customers.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-slate-400 hover:text-white transition">Batal</a>
        </div>
    </form>
</div>
@endsection