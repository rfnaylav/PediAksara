<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request->user()));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        do {
            $memberCode = 'AGT'.Str::upper(Str::random(8));
        } while (User::query()->where('member_code', $memberCode)->exists());

        $user = User::create([
            ...$validated,
            'role' => 'anggota',
            'member_code' => $memberCode,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('catalog.index')->with('status', 'Registrasi berhasil. Kode anggota: '.$memberCode);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog.index');
    }

    private function homeFor(?User $user): string
    {
        return $user?->hasRole('anggota')
            ? route('member.loans.index')
            : route('dashboard');
    }
}
