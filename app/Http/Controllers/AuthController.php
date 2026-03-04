<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $login = $request->string('login')->toString();
        $user = User::query()
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            return back()
                ->withErrors(['login' => 'Invalid credentials provided.'])
                ->withInput($request->only('login'));
        }

        if ($user->status !== 'active') {
            return back()
                ->withErrors(['login' => 'This account is inactive. Contact Leadership.'])
                ->withInput($request->only('login'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Logged in successfully.');
    }

    public function logout(ActionRequest $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Logged out successfully.');
    }
}
