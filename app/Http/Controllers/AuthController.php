<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $key = 'login.'.$request->ip();

        // SEMENTARA: lockout "too many attempts" dimatikan untuk ujian.
        // Pulihkan sebelum produksi: nyahkomen blok di bawah + baris RateLimiter::hit().
        // if (RateLimiter::tooManyAttempts($key, 5)) {
        //     $seconds = RateLimiter::availableIn($key);
        //     throw ValidationException::withMessages([
        //         'email' => trans('auth.throttle', ['seconds' => $seconds]),
        //     ]);
        // }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            request()->session()->regenerate();

            if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        // SEMENTARA: dimatikan untuk ujian — pulihkan sebelum produksi.
        // RateLimiter::hit($key, 900);

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
