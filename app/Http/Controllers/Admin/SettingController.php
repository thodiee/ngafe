<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index() {
        $earn_rate = Setting::firstOrCreate(['key' => 'point_earn_threshold'], ['value' => '10000', 'type' => 'int']);
        $redeem_value = Setting::firstOrCreate(['key' => 'point_redeem_value'], ['value' => '100', 'type' => 'int']);
        return view('admin.settings.loyalty', compact('earn_rate', 'redeem_value'));
    }

    public function update(Request $request) {
        $request->validate([
            'point_earn_threshold' => 'required|numeric|min:1',
            'point_redeem_value' => 'required|numeric|min:1',
        ]);

        Setting::where('key', 'point_earn_threshold')->update(['value' => $request->point_earn_threshold]);
        Setting::where('key', 'point_redeem_value')->update(['value' => $request->point_redeem_value]);

        return redirect()->back()->with('success', 'Pengaturan Loyalitas berhasil diperbarui!');
    }

    public function birthday() {
        $bday_promo_status = Setting::firstOrCreate(['key' => 'bday_promo_status'], ['value' => '0', 'type' => 'boolean']);
        $bday_promo_message = Setting::firstOrCreate(['key' => 'bday_promo_message'], ['value' => 'Gratis 1 Kopi Susu Aren untuk yang berulang tahun!', 'type' => 'string']);
        return view('admin.settings.birthday', compact('bday_promo_status', 'bday_promo_message'));
    }

    public function updateBirthday(Request $request) {
        $request->validate([
            'bday_promo_status' => 'required|in:0,1',
            'bday_promo_message' => 'required|string',
        ]);

        Setting::where('key', 'bday_promo_status')->update(['value' => $request->bday_promo_status]);
        Setting::where('key', 'bday_promo_message')->update(['value' => $request->bday_promo_message]);

        return redirect()->back()->with('success', 'Pengaturan Promo Ulang Tahun berhasil diperbarui!');
    }

    public function attendance() {
        $lat = Setting::firstOrCreate(['key' => 'attendance_lat'], ['value' => '-6.200000', 'type' => 'string']); // Default Jakarta
        $lng = Setting::firstOrCreate(['key' => 'attendance_lng'], ['value' => '106.816666', 'type' => 'string']);
        $radius = Setting::firstOrCreate(['key' => 'attendance_radius'], ['value' => '50', 'type' => 'int']);
        return view('admin.settings.attendance', compact('lat', 'lng', 'radius'));
    }

    public function updateAttendance(Request $request) {
        $request->validate([
            'attendance_lat' => 'required|string',
            'attendance_lng' => 'required|string',
            'attendance_radius' => 'required|numeric|min:10',
        ]);

        Setting::where('key', 'attendance_lat')->update(['value' => $request->attendance_lat]);
        Setting::where('key', 'attendance_lng')->update(['value' => $request->attendance_lng]);
        Setting::where('key', 'attendance_radius')->update(['value' => $request->attendance_radius]);

        return redirect()->back()->with('success', 'Pengaturan Lokasi Absensi berhasil diperbarui!');
    }
}