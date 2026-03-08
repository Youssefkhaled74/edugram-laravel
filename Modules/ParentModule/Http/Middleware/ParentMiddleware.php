<?php

namespace Modules\ParentModule\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RolePermission\Entities\Role;

class ParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('parent.login')
                ->with('error', 'Please login to access parent portal.');
        }

        $user = Auth::user();
        $parentRole = Role::where('name', 'Parent')->first();

        if (!$parentRole || $user->role_id != $parentRole->id) {
            Auth::logout();
            return redirect()->route('parent.login')
                ->with('error', 'Unauthorized access. Parent login required.');
        }

        if (!$user->is_active || $user->status != 1) {
            Auth::logout();
            return redirect()->route('parent.login')
                ->with('error', 'Your account is inactive. Please contact administrator.');
        }

        return $next($request);
    }
}

