<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check()) {
            if (in_array(Auth::user()->role, ['admin', 'owner'])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('hr.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:100'
        ]);

        $credentials['email'] = Str::lower(trim($credentials['email']));
        $throttleKey = 'login:' . $request->ip() . '|' . $credentials['email'];

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            if (in_array(Auth::user()->role, ['admin', 'owner'])) {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('hr.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors(['email' => 'Email atau kata sandi tidak valid.'])->withInput($request->only('email'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}