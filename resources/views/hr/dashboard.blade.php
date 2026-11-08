@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-500">HR Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400">Absensi & Jadwal Shift Pegawai</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(!auth()->user()->face_reference || !auth()->user()->face_verified_at)
                <div class="bg-orange-500/20 text-orange-400 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 border border-orange-500/30">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    @if(!auth()->user()->face_reference)
                        Wajah belum didaftarkan
                    @else
                        Menunggu validasi HR
                    @endif
                    &nbsp;
                    <button onclick="openFaceModal('register')" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1 rounded-lg font-bold text-xs transition">{{ auth()->user()->face_reference ? 'Daftar Ulang' : 'Daftar Wajah' }}</button>
                </div>
            @else
                <div class="bg-emerald-500/20 text-emerald-400 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 border border-emerald-500/30">
                    <i class="fa-solid fa-face-grin-stars"></i> Wajah Terverifikasi
                    <button onclick="openFaceModal('register')" class="text-xs text-slate-400 hover:text-white ml-2" title="Daftar ulang"><i class="fa-solid fa-rotate"></i></button>
                </div>
            @endif

            @if(!$todayAttendance || !$todayAttendance->clock_in)
                <button onclick="openFaceModal('clockin')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 font-bold">
                    <i class="fa-solid fa-right-to-bracket"></i> Clock In
                </button>
            @elseif(!$todayAttendance->clock_out)
                <span class="bg-slate-100 dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> Masuk: {{ $todayAttendance->clock_in }}
                </span>
                <button onclick="openFaceModal('clockout')" class="bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 font-bold">
                    <i class="fa-solid fa-right-from-bracket"></i> Clock Out
                </button>
            @else
                <span class="bg-slate-100 dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-check-double"></i> Absen Selesai
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Attendance Today -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-clipboard-user mr-2 text-purple-500 dark:text-purple-400"></i> Absensi Hari Ini</h3>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[500px] whitespace-nowrap">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th class="pb-3 px-2">Pegawai</th>
                            <th class="pb-3 px-2">Masuk</th>
                            <th class="pb-3 px-2">Keluar</th>
                            <th class="pb-3 px-2">Status</th>
                            <th class="pb-3 px-2">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="py-3 px-2 font-bold text-[#2c2520] dark:text-slate-200">{{ $att->user->name ?? '-' }}</td>
                            <td class="py-3 px-2 font-mono text-emerald-600 dark:text-emerald-400">{{ $att->clock_in }}</td>
                            <td class="py-3 px-2 font-mono text-orange-600 dark:text-orange-400">{{ $att->clock_out ?? '-' }}</td>
                            <td class="py-3 px-2">
                                @if($att->status == 'ontime')
                                    <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs font-bold">Tepat Waktu</span>
                                @else
                                    <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-xs font-bold">Terlambat</span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                @if($att->photo_proof)
                                    <button onclick="showProof('{{ $att->photo_proof }}')" class="text-blue-400 text-xs underline">Lihat</button>
                                @else
                                    <span class="text-slate-500 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">Belum ada absensi hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Schedules Today -->
        <div class="glass-luxury rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-[#2c2520] dark:text-slate-200"><i class="fa-solid fa-calendar-days mr-2 text-pink-500 dark:text-pink-400"></i> Jadwal Shift Hari Ini</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($schedules as $sched)
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-pink-500/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-pink-500/20 rounded-full flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold text-sm">
                                {{ strtoupper(substr($sched->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-[#2c2520] dark:text-white text-sm">{{ $sched->user->name ?? '-' }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $sched->user->role ?? '' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">{{ $sched->shift->name ?? 'Shift' }}</div>
                            <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($sched->shift->start_time ?? '')->format('H:i') }} - {{ \Carbon\Carbon::parse($sched->shift->end_time ?? '')->format('H:i') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">Tidak ada jadwal hari ini</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- FACE MODAL -->
<!-- ============================================================ -->
<div id="faceModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 flex">
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl w-full max-w-lg border border-slate-200 dark:border-slate-700 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="faceModalTitle" class="text-xl font-bold text-[#2c2520] dark:text-white">Scan Wajah</h3>
            <button onclick="closeFaceModal()" class="text-slate-400 hover:text-[#2c2520] dark:hover:text-white text-xl"><i class="fa-solid fa-times"></i></button>
        </div>

        <!-- Status -->
        <div id="faceStatus" class="mb-4 p-3 rounded-lg text-sm text-center hidden"></div>

        <!-- Video Feed -->
        <div class="relative bg-black rounded-xl overflow-hidden mb-4" style="aspect-ratio: 4/3;">
            <video id="videoEl" autoplay muted playsinline class="w-full h-full object-cover"></video>
            <canvas id="overlayCanvas" class="absolute inset-0 w-full h-full" style="opacity:0.8;"></canvas>
            <div id="loadingOverlay" class="absolute inset-0 flex flex-col items-center justify-center bg-black/80">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-400 mb-3"></i>
                <p class="text-sm text-slate-300" id="loadingText">Memuat model pengenalan wajah...</p>
            </div>
        </div>

        <!-- Location Status -->
        <div id="locationStatus" class="mb-4 p-2 rounded-lg text-xs text-center bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-location-dot mr-1"></i> Mengambil lokasi GPS...
        </div>

        <button id="captureBtn" onclick="capture()" disabled class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-camera"></i> <span id="captureBtnText">Ambil Foto & Proses</span>
        </button>
    </div>
</div>

<!-- Proof Image Modal -->
<div id="proofModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 flex">
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 max-w-sm w-full">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-bold text-[#2c2520] dark:text-white">Foto Bukti Absen</h4>
            <button onclick="document.getElementById('proofModal').classList.add('hidden')" class="text-slate-400 hover:text-[#2c2520] dark:hover:text-white"><i class="fa-solid fa-times"></i></button>
        </div>
        <img id="proofImage" src="" class="w-full rounded-xl" alt="Foto Bukti Absen">
    </div>
</div>

<script>
// ============================================================
// GLOBAL STATE
// ============================================================
let currentMode = null; // 'register' | 'clockin' | 'clockout'
let videoStream = null;
let faceApiLoaded = false;
let userLat = null, userLng = null;
let locationOk = false;
let FACE_DETECTION_THRESHOLD = 0.4; // Euclidean distance threshold

const FACE_REFERENCE = @json(auth()->user()->face_reference);
const IS_VERIFIED = @json(auth()->user()->face_verified_at ? true : false);
const CLOCK_TOKEN = "{{ csrf_token() }}";

// ============================================================
// LOAD FACE-API.JS
// ============================================================
function loadFaceApi() {
    return new Promise((resolve, reject) => {
        if (window.faceapi) { resolve(); return; }
        const script = document.createElement('script');
        script.src = '/vendor/face-api.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

async function loadFaceModels() {
    const MODEL_URL = '/models/face-api';
    await Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
    ]);
}

// ============================================================
// OPEN MODAL
// ============================================================
async function openFaceModal(mode) {
    currentMode = mode;
    document.getElementById('faceModal').classList.remove('hidden');
    document.getElementById('faceStatus').classList.add('hidden');
    document.getElementById('captureBtn').disabled = true;
    document.getElementById('loadingOverlay').classList.remove('hidden');

    const titles = {
        'register': 'Daftarkan Wajah Anda',
        'clockin': 'Clock In - Scan Wajah',
        'clockout': 'Clock Out - Scan Wajah'
    };
    document.getElementById('faceModalTitle').innerText = titles[mode];
    document.getElementById('captureBtnText').innerText = mode === 'register' ? 'Daftarkan Wajah' : 'Absen Sekarang';

    // Start camera
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        document.getElementById('videoEl').srcObject = videoStream;
    } catch (e) {
        showStatus('error', 'Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.');
        return;
    }

    // Get location
    getLocation();

    // Load face-api
    try {
        setLoadingText('Memuat model pengenalan wajah...');
        await loadFaceApi();
        await loadFaceModels();
        faceApiLoaded = true;
        document.getElementById('loadingOverlay').classList.add('hidden');
        checkReady();
    } catch (e) {
        showStatus('error', 'Gagal memuat model AI wajah. Periksa koneksi atau file model.');
        console.error(e);
    }
}

function getLocation() {
    if (!navigator.geolocation) {
        setLocationStatus('error', 'Browser tidak mendukung GPS.');
        return;
    }
    setLocationStatus('loading', 'Mengambil lokasi GPS...');
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            locationOk = true;
            setLocationStatus('ok', `Lokasi ditemukan (akurasi ±${Math.round(pos.coords.accuracy)}m)`);
            checkReady();
        },
        (err) => {
            setLocationStatus('error', 'Gagal mendapatkan lokasi GPS. Aktifkan GPS & izin lokasi.');
            locationOk = false;
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
}

function checkReady() {
    if (faceApiLoaded && (locationOk || currentMode === 'register')) {
        document.getElementById('captureBtn').disabled = false;
    }
}

// ============================================================
// CAPTURE & PROCESS
// ============================================================
async function capture() {
    document.getElementById('captureBtn').disabled = true;
    showStatus('loading', 'Mendeteksi wajah...');

    const video = document.getElementById('videoEl');

    // Detect face
    const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
    if (!detection) {
        showStatus('error', 'Wajah tidak terdeteksi. Pastikan wajah Anda terlihat jelas oleh kamera.');
        document.getElementById('captureBtn').disabled = false;
        return;
    }

    const descriptor = Array.from(detection.descriptor); // Float32Array → Array

    if (currentMode === 'register') {
        await doRegisterFace(descriptor);
    } else {
        // Verify face against stored reference
        if (!FACE_REFERENCE) {
            showStatus('error', 'Tidak ada data wajah referensi. Silakan daftar wajah terlebih dahulu.');
            document.getElementById('captureBtn').disabled = false;
            return;
        }
        if (!IS_VERIFIED) {
            showStatus('error', 'Wajah Anda belum divalidasi oleh HR. Harap tunggu.');
            document.getElementById('captureBtn').disabled = false;
            return;
        }

        const storedDescriptor = new Float32Array(JSON.parse(FACE_REFERENCE));
        const distance = faceapi.euclideanDistance(descriptor, storedDescriptor);

        if (distance > FACE_DETECTION_THRESHOLD) {
            showStatus('error', `Peringatan! Wajah tidak cocok dengan akun yang login (jarak: ${distance.toFixed(2)}). Absen ditolak.`);
            document.getElementById('captureBtn').disabled = false;
            return;
        }

        // Face OK - capture low-res photo proof
        const photoProof = captureCompressedPhoto(video, 160, 120);
        await doClockAction(currentMode, descriptor, photoProof);
    }
}

function captureCompressedPhoto(video, width, height) {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, width, height);
    return canvas.toDataURL('image/jpeg', 0.4); // Low quality for small size
}

async function doRegisterFace(descriptor) {
    showStatus('loading', 'Menyimpan data wajah...');
    const descriptorJson = JSON.stringify(Array.from(descriptor));

    const res = await fetch('/hr/register-face', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CLOCK_TOKEN, 'Accept': 'application/json' },
        body: JSON.stringify({ face_reference: descriptorJson })
    });
    const data = await res.json();
    if (data.success) {
        showStatus('success', data.message);
        setTimeout(() => { closeFaceModal(); window.location.reload(); }, 2500);
    } else {
        showStatus('error', data.message || 'Gagal menyimpan data wajah.');
        document.getElementById('captureBtn').disabled = false;
    }
}

async function doClockAction(mode, descriptor, photoProof) {
    showStatus('loading', mode === 'clockin' ? 'Memproses Clock In...' : 'Memproses Clock Out...');
    const url = mode === 'clockin' ? '/hr/clock-in' : '/hr/clock-out';

    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CLOCK_TOKEN, 'Accept': 'application/json' },
        body: JSON.stringify({ latitude: userLat, longitude: userLng, photo_proof: photoProof })
    });
    const data = await res.json();
    if (data.success) {
        showStatus('success', data.message);
        setTimeout(() => { closeFaceModal(); window.location.reload(); }, 2000);
    } else {
        showStatus('error', data.message || 'Terjadi kesalahan.');
        document.getElementById('captureBtn').disabled = false;
    }
}

// ============================================================
// HELPERS
// ============================================================
function showStatus(type, msg) {
    const el = document.getElementById('faceStatus');
    el.classList.remove('hidden', 'bg-red-500/20', 'text-red-400', 'bg-green-500/20', 'text-green-400', 'bg-blue-500/20', 'text-blue-400');
    if (type === 'error') el.classList.add('bg-red-500/20', 'text-red-400');
    else if (type === 'success') el.classList.add('bg-green-500/20', 'text-green-400');
    else el.classList.add('bg-blue-500/20', 'text-blue-400');
    el.innerHTML = `<i class="fa-solid fa-${type === 'error' ? 'circle-xmark' : type === 'success' ? 'circle-check' : 'spinner fa-spin'} mr-2"></i>${msg}`;
}

function setLocationStatus(type, msg) {
    const el = document.getElementById('locationStatus');
    const icons = { loading: 'spinner fa-spin', ok: 'circle-check text-emerald-400', error: 'circle-xmark text-red-400' };
    el.innerHTML = `<i class="fa-solid fa-${icons[type]} mr-1"></i>${msg}`;
    if (type === 'ok') el.className = 'mb-4 p-2 rounded-lg text-xs text-center bg-emerald-500/10 text-emerald-400';
    else if (type === 'error') el.className = 'mb-4 p-2 rounded-lg text-xs text-center bg-red-500/10 text-red-400';
}

function setLoadingText(text) {
    document.getElementById('loadingText').innerText = text;
}

function closeFaceModal() {
    document.getElementById('faceModal').classList.add('hidden');
    if (videoStream) { videoStream.getTracks().forEach(t => t.stop()); videoStream = null; }
    faceApiLoaded = false;
    locationOk = false;
    userLat = null; userLng = null;
}

function showProof(data) {
    document.getElementById('proofImage').src = data;
    document.getElementById('proofModal').classList.remove('hidden');
}
</script>
@endsection