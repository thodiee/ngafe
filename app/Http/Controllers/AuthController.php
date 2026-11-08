<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    private function getHomeRouteForRole(string $role): string {
        return match ($role) {
            'admin', 'owner' => route('admin.dashboard'),
            'hr' => route('admin.users.index'),
            'finance' => route('admin.finance.dashboard'),
            'cashier' => route('pos.menu'),
            'kitchen' => route('kitchen.index'),
            'waiter' => route('waiter.index'),
            'inventory' => route('inventory.index'),
            default => route('hr.dashboard'),
        };
    }

    public function showLogin() {
        if (Auth::check()) {
            return redirect($this->getHomeRouteForRole(Auth::user()->role));
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

            $role = Auth::user()->role;
            $destination = $this->getHomeRouteForRole($role);

            // Clean invalid intended URLs for non-admin roles (e.g. attempting to visit /admin)
            $intended = session()->get('url.intended');
            if ($intended && !in_array($role, ['admin', 'owner'])) {
                $allowedPaths = ['/admin/users', '/admin/hr', '/admin/attendances', '/admin/finance', '/admin/pos/sales-report', '/inventory', '/pos', '/kitchen', '/waiter', '/hr'];
                $isAllowed = false;
                foreach ($allowedPaths as $allowedPath) {
                    if (str_contains($intended, $allowedPath)) {
                        $isAllowed = true;
                        break;
                    }
                }
                if (!$isAllowed) {
                    session()->forget('url.intended');
                }
            }

            return redirect()->intended($destination);
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