<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckDefaultPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If the user is logged in and their password is the default 'P4ssword'
        if ($user && Hash::check('P4ssword', $user->password)) {
            \Illuminate\Support\Facades\View::share('isDefaultPassword', true);
        } else {
            \Illuminate\Support\Facades\View::share('isDefaultPassword', false);
        }

        return $next($request);
    }
}
