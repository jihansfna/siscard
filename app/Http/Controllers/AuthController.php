<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Resolve the home route based on user role.
     */
    private function homeRoute(User $user): string
    {
        return match ($user->role) {
            'admin' => 'dashboard',
            default => 'user.home',
        };
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->homeRoute(Auth::user()));
        }

        return view('login');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->homeRoute(Auth::user()));
        }
        
        return view('register');
    }

    /**
     * Handle a registration request (Web & API).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'badge'    => ['required', 'string', 'max:255', 'unique:users,badge'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['nullable', 'string', 'in:user,admin'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'badge'    => $validated['badge'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'] ?? 'user',
        ]);

        $isApi = $request->expectsJson() || $request->isJson() || $request->wantsJson();

        if ($isApi) {
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'message' => 'User berhasil didaftarkan',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->homeRoute($user))
            ->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    /**
     * Handle a login request (Web & API).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'badge'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $isApi = $request->expectsJson() || $request->isJson() || $request->wantsJson();

        // Case-sensitive badge check
        $user = User::whereRaw('BINARY badge = ?', [$credentials['badge']])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            $error = ['badge' => 'Badge atau password yang Anda masukkan salah.'];

            if ($isApi) {
                return response()->json(['message' => $error['badge']], 401);
            }

            return back()->withInput($request->only('badge'))->withErrors($error);
        }

        if ($isApi) {
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token'   => $token,
                'user'    => $user,
            ]);
        }

        // Web login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route($this->homeRoute($user)))
            ->with('success', 'Login berhasil!');
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            // API logout
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout berhasil']);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Handle password reset request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'badge' => ['required', 'string'],
        ]);

        $user = User::whereRaw('BINARY badge = ?', [$request->badge])->first();

        if (!$user) {
            return back()->withErrors(['badge' => 'Badge ID tidak ditemukan dalam sistem.']);
        }

        $user->update([
            'password' => Hash::make('P4ssword')
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil direset ke password default.');
    }
}
