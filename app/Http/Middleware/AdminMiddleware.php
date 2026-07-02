<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (auth()->check()) {
            $userRole = auth()->user()->role;
            if (empty($roles)) {
                // If no roles specified, default to only admin
                if ($userRole === 'admin') {
                    return $next($request);
                }
            } else {
                // If roles are specified, check if user has one of them, plus admin always has access
                if ($userRole === 'admin' || in_array($userRole, $roles)) {
                    return $next($request);
                }
            }
        }
        
        return redirect('/')->with('error', 'Unauthorized Access');
    }
}
