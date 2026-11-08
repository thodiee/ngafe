@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} Waste</h2>
</div>
<div class="bg-slate-800 rounded-xl p-4 sm:p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.wastes.update', $item) : route('admin.wastes.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Waste Date</label>
            <input type="date" name="waste_date" value="{{ old('waste_date', $item->waste_date ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Location</label>
            <select name="location" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
                <option value="gudang" {{ old('location', $item->location ?? '') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                <option value="dapur" {{ old('location', $item->location ?? '') == 'dapur' ? 'selected' : '' }}>Dapur (Bar/Kitchen)</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Notes</label>
            <input type="text" name="notes" value="{{ old('notes', $item->notes ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-2.5 rounded-lg transition">Simpan</button>
            <a href="{{ route('admin.wastes.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-slate-400 hover:text-white transition">Batal</a>
        </div>
    </form>
</div>
@endsection
