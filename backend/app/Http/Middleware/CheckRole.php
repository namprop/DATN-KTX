<?php

namespace App\Http\Middleware;

use App\Utilities\Constant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.`
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => "Unaithorized"
            ], 401);
        }

        $roleMap = [
            'admin' => Constant::ROLE_ADMIN,
            'staff' => Constant::ROLE_STAFF,
            'student' => Constant::ROLE_STUDENT,
            'parent' => Constant::ROLE_PARENT,
            'parentstudent' => Constant::ROLE_PARENT,
        ];
        $allowedRoles = collect(explode('|', $role))
            ->map(fn (string $item) => $roleMap[strtolower(trim($item))] ?? null)
            ->filter()
            ->all();

        if (!in_array(Auth::user()->role, $allowedRoles, true)) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden',
            ], 403);
        }



        return $next($request);
    }
}
