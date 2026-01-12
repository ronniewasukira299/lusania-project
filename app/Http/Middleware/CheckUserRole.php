<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string ...$roles  // Supports multiple roles, e.g., 'role:staff,admin'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has ANY of the allowed roles
        if (! in_array($user->role, $roles)) {
            abort(403, 'Unauthorized: Insufficient role.');
            // Or redirect: return redirect('/')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}