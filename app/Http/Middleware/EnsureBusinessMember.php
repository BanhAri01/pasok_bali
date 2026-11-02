<?php

namespace App\Http\Middleware;

use App\Support\CurrentBusiness;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessMember
{
    public function __construct(private CurrentBusiness $current) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->business_id === null) {
            return $user?->isAdmin() ? redirect()->route('admin.index') : redirect()->route('login');
        }

        $business = $user->business;

        if (! $business || $business->demoExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Sesi demo sudah berakhir. Silakan mulai demo baru.');
        }

        $this->current->set($business->id);
        $request->attributes->set('business', $business);

        return $next($request);
    }
}
