<?php

namespace Modules\ParentModule\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class CheckImpersonation
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
        // Share impersonation status with all views
        View::share('isImpersonating', Session::get('is_impersonating', false));
        View::share('impersonatingParentName', Session::get('impersonating_parent_name', null));
        
        return $next($request);
    }
}

