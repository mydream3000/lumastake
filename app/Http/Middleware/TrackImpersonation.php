<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class TrackImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Делаем переменную impersonating доступной во всех view
        View::share('impersonating', session()->has('admin_impersonate_id'));
        View::share('impersonator_id', session()->get('admin_impersonate_id'));

        return $next($request);
    }
}
