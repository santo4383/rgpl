<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized action. Please log in.');
        }

        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $role) {
                $trimmed = trim($role);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        $hasRole = false;
        foreach ($allowedRoles as $role) {
            if ($request->user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            abort(403, 'Unauthorized action. Required role: '.implode(', ', $allowedRoles));
        }

        return $next($request);
    }
}
