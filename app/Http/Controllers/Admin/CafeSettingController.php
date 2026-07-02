<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;

class CafeSettingController extends Controller
{
    // ========== PROFIL KAFE ==========
    public function profile() {
        $fields = ['cafe_name', 'cafe_tagline', 'cafe_address', 'cafe_phone', 'cafe_email', 'cafe_logo'];
        $settings = [];
        foreach ($fields as $key) {
            $settings[$key] = Setting::firstOrCreate(['key' => $key], ['value' => '', 'type' => 'string'])->value;
        }
        return view('admin.cafe.profile', compact('settings'));
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'cafe_name'    => 'required|string|max:255',
            'cafe_tagline' => 'nullable|string|max:255',
            'cafe_address' => 'nullable|string',
            'cafe_phone'   => 'nullable|string|max:30',
            'cafe_email'   => 'nullable|email|max:255',
        ]);

        $fields = ['cafe_name', 'cafe_tagline', 'cafe_address', 'cafe_phone', 'cafe_email'];
        foreach ($fields as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, ''), 'type' => 'string']);
        }

        // Handle logo upload
        if ($request->hasFile('cafe_logo')) {
            $request->validate(['cafe_logo' => 'image|max:2048']);
            $path = $request->file('cafe_logo')->store('public/logo');
            $url = Storage::url($path);
            Setting::updateOrCreate(['key' => 'cafe_logo'], ['value' => $url, 'type' => 'string']);
        }

        return back()->with('success', 'Profil kafe berhasil diperbarui!');
    }

    // ========== MASTER SATUAN ==========
    public function units() {
        $units = Unit::orderBy('name')->get();
        return view('admin.cafe.units', compact('units'));
    }

    public function storeUnit(Request $request) {
        $request->validate(['name' => 'required|string|max:100', 'abbreviation' => 'required|string|max:20']);
        Unit::create($request->only('name', 'abbreviation'));
        return back()->with('success', 'Satuan berhasil ditambahkan!');
    }

    public function updateUnit(Request $request, Unit $unit) {
        $request->validate(['name' => 'required|string|max:100', 'abbreviation' => 'required|string|max:20']);
        $unit->update($request->only('name', 'abbreviation'));
        return back()->with('success', 'Satuan berhasil diperbarui!');
    }

    public function destroyUnit(Unit $unit) {
        $unit->delete();
        return back()->with('success', 'Satuan dihapus!');
    }

    // ========== METODE PEMBAYARAN ==========
    public function paymentMethods() {
        $methods = PaymentMethod::orderBy('name')->get();
        return view('admin.cafe.payment_methods', compact('methods'));
    }

    public function storePaymentMethod(Request $request) {
        $request->validate([
            'name'         => 'required|string|max:100',
            'icon'         => 'nullable|string|max:100',
            'has_surcharge'=> 'required|boolean',
            'surcharge_rate'=> 'nullable|numeric|min:0|max:100',
        ]);
        PaymentMethod::create([
            'name'          => $request->name,
            'icon'          => $request->icon ?? 'fa-credit-card',
            'is_active'     => true,
            'has_surcharge' => $request->has_surcharge,
            'surcharge_rate'=> $request->has_surcharge ? $request->surcharge_rate : 0,
        ]);
        return back()->with('success', 'Metode pembayaran berhasil ditambahkan!');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $method) {
        $request->validate([
            'name'          => 'required|string|max:100',
            'icon'          => 'nullable|string|max:100',
            'is_active'     => 'required|boolean',
            'has_surcharge' => 'required|boolean',
            'surcharge_rate'=> 'nullable|numeric|min:0|max:100',
        ]);
        $method->update([
            'name'          => $request->name,
            'icon'          => $request->icon ?? 'fa-credit-card',
            'is_active'     => $request->is_active,
            'has_surcharge' => $request->has_surcharge,
            'surcharge_rate'=> $request->has_surcharge ? $request->surcharge_rate : 0,
        ]);
        return back()->with('success', 'Metode pembayaran diperbarui!');
    }

    public function destroyPaymentMethod(PaymentMethod $method) {
        $method->delete();
        return back()->with('success', 'Metode pembayaran dihapus!');
    }

    // ========== KONFIGURASI PAJAK ==========
    public function tax() {
        $taxEnabled  = Setting::firstOrCreate(['key' => 'tax_enabled'],  ['value' => '0',     'type' => 'boolean'])->value;
        $taxRate     = Setting::firstOrCreate(['key' => 'tax_rate'],     ['value' => '11',    'type' => 'float'])->value;
        $taxLabel    = Setting::firstOrCreate(['key' => 'tax_label'],    ['value' => 'PPN',   'type' => 'string'])->value;
        $taxInclusive= Setting::firstOrCreate(['key' => 'tax_inclusive'],['value' => '0',     'type' => 'boolean'])->value;
        $serviceCharge = Setting::firstOrCreate(['key' => 'service_charge_enabled'], ['value' => '0', 'type' => 'boolean'])->value;
        $serviceRate   = Setting::firstOrCreate(['key' => 'service_charge_rate'],    ['value' => '5', 'type' => 'float'])->value;
        return view('admin.cafe.tax', compact('taxEnabled','taxRate','taxLabel','taxInclusive','serviceCharge','serviceRate'));
    }

    public function updateTax(Request $request) {
        $request->validate([
            'tax_rate'   => 'required|numeric|min:0|max:100',
            'tax_label'  => 'required|string|max:50',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
        ]);
        Setting::updateOrCreate(['key' => 'tax_enabled'],          ['value' => $request->boolean('tax_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'tax_rate'],             ['value' => $request->tax_rate, 'type' => 'float']);
        Setting::updateOrCreate(['key' => 'tax_label'],            ['value' => $request->tax_label, 'type' => 'string']);
        Setting::updateOrCreate(['key' => 'tax_inclusive'],        ['value' => $request->boolean('tax_inclusive') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'service_charge_enabled'],['value' => $request->boolean('service_charge_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'service_charge_rate'],  ['value' => $request->service_charge_rate, 'type' => 'float']);
        return back()->with('success', 'Konfigurasi pajak & biaya layanan berhasil diperbarui!');
    }

    // ========== GERBANG PEMBAYARAN (MIDTRANS) ==========
    public function paymentSettings() {
        $midtransMode = Setting::firstOrCreate(['key' => 'midtrans_mode'], ['value' => 'simulation', 'type' => 'string'])->value;
        $serverKey = Setting::firstOrCreate(['key' => 'midtrans_server_key'], ['value' => '', 'type' => 'string'])->value;
        $clientKey = Setting::firstOrCreate(['key' => 'midtrans_client_key'], ['value' => '', 'type' => 'string'])->value;
        $isProduction = Setting::firstOrCreate(['key' => 'midtrans_is_production'], ['value' => '0', 'type' => 'boolean'])->value;

        return view('admin.cafe.payment_settings', compact('midtransMode', 'serverKey', 'clientKey', 'isProduction'));
    }

    public function updatePaymentSettings(Request $request) {
        $request->validate([
            'midtrans_mode' => 'required|in:simulation,midtrans',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
        ]);

        Setting::updateOrCreate(['key' => 'midtrans_mode'], ['value' => $request->midtrans_mode]);
        Setting::updateOrCreate(['key' => 'midtrans_server_key'], ['value' => $request->midtrans_server_key ?? '']);
        Setting::updateOrCreate(['key' => 'midtrans_client_key'], ['value' => $request->midtrans_client_key ?? '']);
        Setting::updateOrCreate(['key' => 'midtrans_is_production'], ['value' => $request->boolean('midtrans_is_production') ? '1' : '0']);

        return back()->with('success', 'Konfigurasi gerbang pembayaran berhasil diperbarui!');
    }
}
