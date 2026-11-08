@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Supplier Pemasok</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Data distributor atau penyedia bahan baku operasional.</p>
</div>
<div class="bg-slate-800 rounded-xl p-4 sm:p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.suppliers.update', $item) : route('admin.suppliers.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Nama Perusahaan / Supplier</label>
            <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Kontak Person (Sales/PIC)</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $item->contact_person ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $item->phone ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $item->email ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Alamat Gudang / Kantor</label>
            <textarea name="address" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">{{ old('address', $item->address ?? '') }}</textarea>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Supplier
            </button>
            <a href="{{ route('admin.suppliers.index') }}" class="w-full sm:w-auto text-center text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
