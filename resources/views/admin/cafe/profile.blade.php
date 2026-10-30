@extends('admin.layout')
@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="flex items-center gap-4 mb-8">
    <div class="w-12 h-12 bg-indigo-500/20 text-indigo-400 rounded-xl flex items-center justify-center text-2xl"><i class="fa-solid fa-store"></i></div>
    <div>
        <h2 class="text-3xl font-bold">Profil Kafe</h2>
        <p class="text-slate-400">Informasi dasar yang tampil di struk dan halaman publik.</p>
    </div>
</div>

<form action="{{ route('admin.cafe.profile.update') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-5">
    @csrf

    {{-- Logo --}}
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 flex items-center gap-6">
        <div class="w-24 h-24 rounded-2xl bg-slate-900 border-2 border-dashed border-slate-600 flex items-center justify-center overflow-hidden flex-shrink-0">
            @if($settings['cafe_logo'])
                <img src="{{ $settings['cafe_logo'] }}" class="w-full h-full object-contain" id="logoPreview" alt="Logo">
            @else
                <i class="fa-solid fa-image text-slate-500 text-3xl" id="logoPlaceholder"></i>
                <img id="logoPreview" class="w-full h-full object-contain hidden" alt="Logo">
            @endif
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Logo Kafe</label>
            <input type="file" name="cafe_logo" accept="image/*" onchange="previewLogo(this)"
                class="block text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer hover:file:bg-indigo-500">
            <p class="text-xs text-slate-500 mt-1">PNG/JPG, maks. 2MB. Akan tampil di struk & header.</p>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4">
        <div>
            <label class="block text-sm text-slate-400 mb-1">Nama Kafe <span class="text-red-400">*</span></label>
            <input type="text" name="cafe_name" value="{{ $settings['cafe_name'] }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 outline-none transition" required placeholder="Ngafe Coffee & More">
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Tagline</label>
            <input type="text" name="cafe_tagline" value="{{ $settings['cafe_tagline'] }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 outline-none transition" placeholder="A place where every cup tells a story">
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Alamat Lengkap</label>
            <textarea name="cafe_address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 outline-none transition resize-none" placeholder="Jl. ...">{{ $settings['cafe_address'] }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">No. Telepon / WhatsApp</label>
                <input type="text" name="cafe_phone" value="{{ $settings['cafe_phone'] }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 outline-none transition" placeholder="0812...">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Email</label>
                <input type="email" name="cafe_email" value="{{ $settings['cafe_email'] }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 outline-none transition" placeholder="info@cafe.com">
            </div>
        </div>
    </div>

    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-save"></i> Simpan Profil
    </button>
</form>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
