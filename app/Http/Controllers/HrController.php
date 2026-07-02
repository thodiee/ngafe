<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

class HrController extends Controller
{
    public function dashboard() {
        $attendances = Attendance::with('user')->whereDate('date', today())->get();
        $schedules = Schedule::with(['user', 'shift'])->where('date', today()->format('Y-m-d'))->get();
        $todayAttendance = Attendance::where('user_id', auth()->id())->whereDate('date', today())->first();
        return view('hr.dashboard', compact('attendances', 'schedules', 'todayAttendance'));
    }

    public function clockIn(Request $request) {
        $user = auth()->user();

        // Pastikan wajah sudah divalidasi
        if (!$user->face_reference || !$user->face_verified_at) {
            return response()->json(['success' => false, 'message' => 'Wajah belum terdaftar/divalidasi HR. Silakan daftar wajah terlebih dahulu.'], 403);
        }

        // Pastikan belum clock in
        $existing = Attendance::where('user_id', $user->id)->whereDate('date', today())->first();
        if ($existing && $existing->clock_in) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan Clock In hari ini.'], 422);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo_proof' => 'required|string',
        ]);

        // Hitung jarak dari lokasi kafe
        $cafeLat = (float) Setting::where('key', 'attendance_lat')->value('value');
        $cafeLng = (float) Setting::where('key', 'attendance_lng')->value('value');
        $radius  = (int)   Setting::where('key', 'attendance_radius')->value('value');
        $distance = $this->haversineDistance($request->latitude, $request->longitude, $cafeLat, $cafeLng);

        if ($distance > $radius) {
            return response()->json(['success' => false, 'message' => "Anda berada " . round($distance) . " meter dari kafe. Hanya diizinkan dalam radius {$radius} meter."], 403);
        }

        // Cek telat
        $schedule = Schedule::with('shift')->where('user_id', $user->id)->where('date', today()->format('Y-m-d'))->first();
        $status = 'ontime';
        if ($schedule && $schedule->shift) {
            $shiftStart = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->shift->start_time);
            if (Carbon::now()->gt($shiftStart->addMinutes(10))) {
                $status = 'late';
            }
        }

        // Kompres photo_proof menjadi thumbnail kecil (hanya simpan versi kecil)
        $photoProof = $request->photo_proof; // Already compressed in JS

        Attendance::create([
            'user_id'     => $user->id,
            'date'        => today(),
            'clock_in'    => Carbon::now()->format('H:i:s'),
            'status'      => $status,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'photo_proof' => $photoProof,
        ]);

        $msg = $status === 'late' ? 'Clock In berhasil (Terlambat)' : 'Clock In berhasil (Tepat Waktu)!';
        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function clockOut(Request $request) {
        $user = auth()->user();

        if (!$user->face_reference || !$user->face_verified_at) {
            return response()->json(['success' => false, 'message' => 'Wajah belum divalidasi HR.'], 403);
        }

        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', today())->first();
        if (!$attendance || !$attendance->clock_in) {
            return response()->json(['success' => false, 'message' => 'Belum Clock In hari ini.'], 422);
        }
        if ($attendance->clock_out) {
            return response()->json(['success' => false, 'message' => 'Anda sudah Clock Out hari ini.'], 422);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo_proof' => 'required|string',
        ]);

        $cafeLat  = (float) Setting::where('key', 'attendance_lat')->value('value');
        $cafeLng  = (float) Setting::where('key', 'attendance_lng')->value('value');
        $radius   = (int)   Setting::where('key', 'attendance_radius')->value('value');
        $distance = $this->haversineDistance($request->latitude, $request->longitude, $cafeLat, $cafeLng);

        if ($distance > $radius) {
            return response()->json(['success' => false, 'message' => "Anda berada " . round($distance) . " meter dari kafe. Hanya diizinkan dalam radius {$radius} meter."], 403);
        }

        $attendance->update(['clock_out' => Carbon::now()->format('H:i:s')]);
        return response()->json(['success' => true, 'message' => 'Clock Out berhasil!']);
    }

    public function registerFace(Request $request) {
        $request->validate(['face_reference' => 'required|string']);
        $user = auth()->user();
        $user->face_reference = $request->face_reference;
        $user->face_verified_at = null; // Reset - perlu validasi HR lagi
        $user->save();
        return response()->json(['success' => true, 'message' => 'Wajah berhasil didaftarkan. Menunggu validasi oleh HR.']);
    }

    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float {
        $earthRadius = 6371000; // Dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}