@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <div class="inline-block p-4 rounded-full bg-brand-500/20 text-brand-500 mb-4">
            <i class="fa-solid fa-briefcase text-4xl"></i>
        </div>
        <h1 class="text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-brand-600 via-brand-500 to-amber-600 dark:from-brand-400 dark:via-brand-500 dark:to-amber-500">Join Our Team</h1>
        <p class="text-slate-500 dark:text-slate-400 text-lg">Build your career with Ngafe. We are always looking for passionate individuals.</p>
    </div>

    <div class="space-y-6 mb-12">
        @forelse($jobs as $job)
        <div class="glass-luxury rounded-2xl shadow-xl p-6 md:p-8 hover:border-brand-500/50 transition duration-300 group">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-[#2c2520] dark:text-white mb-2 group-hover:text-brand-500 dark:group-hover:text-brand-400 transition">{{ $job->title }}</h2>
                    <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-3 py-1 rounded-full text-sm border border-slate-200 dark:border-slate-600"><i class="fa-solid fa-location-dot mr-1"></i> Full Time / On-site</span>
                </div>
                <button onclick="openApplyModal({{ $job->id }}, '{{ addslashes($job->title) }}')" class="bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shrink-0">
                    Apply Now <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>
            
            <div class="prose max-w-none text-slate-600 dark:text-slate-300">
                <div class="mb-4">
                    <h3 class="text-[#2c2520] dark:text-white text-lg font-semibold mb-2">Description:</h3>
                    <p>{{ $job->description }}</p>
                </div>
                <div>
                    <h3 class="text-[#2c2520] dark:text-white text-lg font-semibold mb-2">Requirements:</h3>
                    <p class="whitespace-pre-line">{{ $job->requirements }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-luxury rounded-2xl p-12 text-center text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-inbox text-5xl mb-4 text-slate-300 dark:text-slate-600"></i>
            <h3 class="text-xl font-bold text-[#2c2520] dark:text-white mb-2">No Open Vacancies</h3>
            <p>We are not actively hiring right now, but please check back later.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal -->
<div id="applyModal" class="fixed inset-0 bg-black/60 dark:bg-slate-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden animate-[fadeIn_0.2s]">
        <div class="bg-slate-50 dark:bg-slate-900 px-6 py-4 flex justify-between items-center border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-xl text-[#2c2520] dark:text-white">Apply for <span id="modalJobTitle" class="text-brand-500">Role</span></h3>
            <button onclick="closeApplyModal()" class="text-slate-400 hover:text-[#2c2520] dark:hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="job_vacancy_id" id="modalJobId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="applicant_name" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Resume / CV (PDF only, max 2MB)</label>
                    <input type="file" name="cv" accept=".pdf" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-[#2c2520] dark:text-white focus:border-brand-500 outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-500/20 file:text-brand-600 dark:file:text-brand-400 hover:file:bg-brand-500/30">
                </div>
            </div>
            
            <div class="mt-8">
                <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-3 rounded-xl shadow-lg transition active:scale-95">Submit Application</button>
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