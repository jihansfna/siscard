<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: middleware('role:admin') or middleware('role:user') or middleware('role:admin,user')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles)) {
            // Redirect based on their actual role
            if ($user) {
                return match ($user->role) {
                    'admin' => redirect()->route('dashboard'),
                    'user'  => redirect()->route('user.home'),
                    default => redirect()->route('login'),
                };
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
