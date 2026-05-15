<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'username' => 'Invalid username or password.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();

        ActivityLogService::log(Auth::id(), 'login', 'auth', 'User logged in.', $request);

        // Drivers go to their mobile portal, everyone else to the dashboard
        $intended = Auth::user()->isDriver()
            ? route('driver.index')
            : route('dashboard');

        return redirect()->intended($intended);
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLogService::log(Auth::id(), 'logout', 'auth', 'User logged out.', $request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
