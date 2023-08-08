<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // If the user is authenticated, redirect to the 'admin' route
            return redirect()->route('admin');
        }

        // If the current route is the 'login' route, continue to the next middleware
        if ($request->routeIs('login')) {
            return $next($request);
        }

        // If the user is not authenticated and the current route is not 'login',
        // redirect to the 'login' route
        return redirect()->route('login');
    }
}
