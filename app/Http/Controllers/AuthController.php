<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
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
        return match ($user->peran) {
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
            'nama'     => ['required', 'string', 'max:255'],
            'badge'    => ['required', 'string', 'max:255', 'unique:users,badge'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'peran'    => ['nullable', 'string', 'in:user,admin'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'badge.required' => 'Badge wajib diisi.',
            'badge.unique' => 'Badge ID sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus :min karakter.',
        ]);

        $user = User::create([
            'nama'     => $validated['nama'],
            'badge'    => $validated['badge'],
            'password' => Hash::make($validated['password']),
            'peran'    => $validated['peran'] ?? 'user',
        ]);

        $isApi = $request->expectsJson() || $request->isJson() || $request->wantsJson();

        if ($isApi) {
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'message' => 'Pengguna berhasil didaftarkan',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->homeRoute($user))
            ->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $user->nama . '.');
    }

    /**
     * Handle a login request (Web & API).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'badge'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $isApi = $request->expectsJson() || $request->isJson() || $request->wantsJson();

        // Case-sensitive badge check
        $user = User::whereRaw('BINARY badge = ?', [$credentials['badge']])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            $error = ['badge' => 'Badge atau password salah.'];

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

        // Block login if employee is inactive (non-admin role)
        if ($user->peran !== 'admin') {
            $karyawan = Karyawan::where('badge', $user->badge)->first();
            if ($karyawan && $karyawan->tanggal_keluar && Carbon::parse($karyawan->tanggal_keluar)->lt(now()->startOfDay())) {
                $error = ['badge' => 'Status akun Anda saat ini Tidak Aktif. Silakan hubungi HRD untuk informasi lebih lanjut.'];
                if ($isApi) {
                    return response()->json(['message' => $error['badge']], 403);
                }
                return back()->withInput($request->only('badge'))->withErrors($error);
            }
        }

        // Web login
        // Track login count to differentiate first-time vs returning users
        $isFirstLogin = $user->jumlah_login === 0 || $user->jumlah_login === null;
        $user->increment('jumlah_login');

        Auth::login($user);
        $request->session()->regenerate();

        $greeting = $isFirstLogin
            ? 'Selamat datang, ' . $user->nama . '!'
            : 'Selamat datang kembali, ' . $user->nama . '!';

        return redirect()->intended(route($this->homeRoute($user)))
            ->with('success', $greeting);
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
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Step 1: Verify badge and return security question.
     */
    public function verifyBadge(Request $request)
    {
        $request->validate([
            'badge' => ['required', 'string', 'max:255'],
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
        ]);

        $user = User::whereRaw('BINARY badge = ?', [$request->badge])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Badge ID tidak ditemukan dalam sistem.',
            ], 422);
        }

        if (!$user->pertanyaan_rahasia || !$user->jawaban_rahasia) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini belum memiliki pertanyaan keamanan. Silakan hubungi HRD untuk mengatur ulang kata sandi.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'pertanyaan_rahasia' => $user->pertanyaan_rahasia,
            'badge' => $user->badge,
        ]);
    }

    /**
     * Step 2: Verify security answer.
     */
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'badge' => ['required', 'string', 'max:255'],
            'jawaban_rahasia' => ['required', 'string', 'max:255'],
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
            'jawaban_rahasia.required' => 'Jawaban keamanan wajib diisi.',
        ]);

        $user = User::whereRaw('BINARY badge = ?', [$request->badge])->first();

        if (!$user || !$user->pertanyaan_rahasia || !$user->jawaban_rahasia) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi gagal. Silakan coba lagi.',
            ], 422);
        }

        $answerNormalized = strtolower(trim($request->jawaban_rahasia));

        if (!Hash::check($answerNormalized, $user->jawaban_rahasia)) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban keamanan tidak sesuai.',
            ], 422);
        }

        // Store a temporary token in session to authorize password reset
        $resetToken = bin2hex(random_bytes(32));
        session(['password_reset_token' => $resetToken, 'password_reset_badge' => $user->badge]);

        return response()->json([
            'success' => true,
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Step 3: Reset password with new password after security verification.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'badge' => ['required', 'string', 'max:255'],
            'reset_token' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
            'reset_token.required' => 'Token reset tidak valid.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'new_password.min' => 'Kata sandi minimal :min karakter.',
        ]);

        // Verify the reset token from session
        $sessionToken = session('password_reset_token');
        $sessionBadge = session('password_reset_badge');

        if (!$sessionToken || $sessionToken !== $request->reset_token || $sessionBadge !== $request->badge) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi reset tidak valid. Silakan ulangi proses dari awal.',
            ], 422);
        }

        $user = User::whereRaw('BINARY badge = ?', [$request->badge])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Clear the session tokens
        session()->forget(['password_reset_token', 'password_reset_badge']);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.',
        ]);
    }

    /**
     * Handle password change request.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password yang diberikan tidak sesuai dengan password Anda saat ini.',
            ]);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'Password baru tidak boleh sama dengan password saat ini.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        $homeRoute = $this->homeRoute($user);

        return redirect()->route($homeRoute)->with('success', 'Password berhasil diperbarui.');
    }

}
