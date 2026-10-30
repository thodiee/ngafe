@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} Supplier</h2>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.suppliers.update', $item) : route('admin.suppliers.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Contact person</label>
            <input type="text" name="contact_person" value="{{ old('contact_person', $item->contact_person ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" >
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $item->phone ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" >
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Email</label>
            <input type="text" name="email" value="{{ old('email', $item->email ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" >
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Address</label>
            <input type="text" name="address" value="{{ old('address', $item->address ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" >
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition">Simpan</button>
            <a href="{{ route('admin.suppliers.index') }}" class="ml-4 text-slate-400 hover:text-white">Batal</a>
        </div>
    </form>
</div>
@endsection
