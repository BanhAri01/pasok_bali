<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Services\DemoSandbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $key = 'masuk:'.mb_strtolower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.']);
        }

        if (! Auth::attempt($credentials, $request->boolean('ingat'))) {
            RateLimiter::hit($key, 300);

            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended($this->home($request->user()));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'types' => config('pasok.business_types'),
            'regencies' => config('pasok.regencies'),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:120'],
            'business_type' => ['required', Rule::in(array_keys(config('pasok.business_types')))],
            'regency' => ['required', Rule::in(config('pasok.regencies'))],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($data) {
            $business = Business::query()->create([
                'name' => $data['business_name'],
                'type' => $data['business_type'],
                'regency' => $data['regency'],
            ]);

            return User::query()->create([
                'business_id' => $business->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::OWNER,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function demo(Request $request, DemoSandbox $sandbox): RedirectResponse
    {
        abort_unless(config('pasok.demo.enabled'), 404);

        $key = 'demo:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, (int) config('pasok.demo.per_ip_daily'))) {
            return back()->withErrors(['demo' => 'Batas demo harian dari jaringan ini sudah tercapai. Coba lagi besok.']);
        }

        RateLimiter::hit($key, 86400);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
        }

        Auth::login($sandbox->create());
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Akun demo siap. Semua data di dalamnya adalah contoh fiktif.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function home(User $user): string
    {
        return $user->isAdmin() ? route('admin.index') : route('dashboard');
    }
}
