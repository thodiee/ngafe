@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-brand-500/20 text-brand-500 border border-brand-500/30">
            <i class="fa-solid fa-store text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Profil Kafe</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Informasi identitas bisnis yang dicetak pada struk kasir dan halaman pemesanan.</p>
        </div>
    </div>

    <form action="{{ route('admin.cafe.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Logo Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Logo Identitas Kafe</h3>
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="w-28 h-28 rounded-2xl bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                    @if($settings['cafe_logo'])
                        <img src="{{ $settings['cafe_logo'] }}" class="w-full h-full object-contain p-2" id="logoPreview" alt="Logo">
                    @else
                        <i class="fa-solid fa-image text-slate-400 text-3xl" id="logoPlaceholder"></i>
                        <img id="logoPreview" class="w-full h-full object-contain p-2 hidden" alt="Logo">
                    @endif
                </div>
                <div class="w-full space-y-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Unggah File Logo Baru</label>
                    <input type="file" name="cafe_logo" accept="image/*" onchange="previewLogo(this)"
                        class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-500 file:text-white file:cursor-pointer hover:file:bg-brand-600 transition">
                    <p class="text-xs text-slate-400">Format disarankan: PNG transparan atau JPG, ukuran maksimal 2MB.</p>
                </div>
            </div>
        </div>

        {{-- General Info Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Detail Usaha</h3>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Nama Kafe <span class="text-red-500">*</span>
                </label>
                <input type="text" name="cafe_name" value="{{ $settings['cafe_name'] }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required placeholder="Ngafe Coffee & Space">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tagline / Slogan</label>
                <input type="text" name="cafe_tagline" value="{{ $settings['cafe_tagline'] }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" placeholder="Artisan Coffee & Cozy Workspace">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                <textarea name="cafe_address" rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition resize-none" placeholder="Jl. Melati No. 12, Jakarta Selatan">{{ $settings['cafe_address'] }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">No. Telepon / WhatsApp</label>
                    <input type="text" name="cafe_phone" value="{{ $settings['cafe_phone'] }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" placeholder="081234567890">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Email Bisnis</label>
                    <input type="email" name="cafe_email" value="{{ $settings['cafe_email'] }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" placeholder="contact@ngafe.id">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-brand-500/20 active:scale-95 transition text-sm">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Perubahan Profil</span>
            </button>
        </div>
    </form>
</div>

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
