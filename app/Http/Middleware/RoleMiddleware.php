<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Penggunaan di routes:
     *   ->middleware('role:super_admin')
     *   ->middleware('role:super_admin,hrd')   // boleh salah satu
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth()->check()) {
            return redirect()->route('login');
        }
 
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403);
        }
 
        return $next($request);
    }
}
