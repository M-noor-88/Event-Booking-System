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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        // Convert enum object to its backed value (string)
        $role = $user->role instanceof \App\Enums\Role
            ? $user->role->value
            : (string) $user->role;

        if (! in_array($role, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized – insufficient role.',
                'your_role' => $role,
                'allowed_roles' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
