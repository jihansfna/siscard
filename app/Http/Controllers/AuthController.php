<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

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
                'message' => 'User successfully registered',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->homeRoute($user))
            ->with('success', 'Registration successful! Welcome, ' . $user->name . '.');
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
            $error = ['badge' => 'Invalid badge or password.'];

            if ($isApi) {
                return response()->json(['message' => $error['badge']], 401);
            }

            return back()->withInput($request->only('badge'))->withErrors($error);
        }

        if ($isApi) {
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => $user,
            ]);
        }

        // Block login if employee is inactive (non-admin role)
        if ($user->role !== 'admin') {
            $employee = Employee::where('badge', $user->badge)->first();
            if ($employee && $employee->end_date && Carbon::parse($employee->end_date)->lt(now()->startOfDay())) {
                $error = ['badge' => 'Your account status is currently Inactive. Please contact HRD for more information.'];
                if ($isApi) {
                    return response()->json(['message' => $error['badge']], 403);
                }
                return back()->withInput($request->only('badge'))->withErrors($error);
            }
        }

        // Web login
        // Track login count to differentiate first-time vs returning users
        $isFirstLogin = $user->login_count === 0 || $user->login_count === null;
        $user->increment('login_count');

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $greeting = $isFirstLogin
            ? 'Welcome, ' . $user->name . '!'
            : 'Welcome back, ' . $user->name . '!';

        return redirect()->intended(route($this->homeRoute($user)))
            ->with('success', $greeting);
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            // API logout
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout successful']);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have successfully logged out.');
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
            return back()->withErrors(['badge' => 'Badge ID not found in the system.']);
        }

        $user->update([
            'password' => Hash::make('P4ssword')
        ]);

        return redirect()->route('login')->with('success', 'Password successfully reset to default password.');
    }
}
