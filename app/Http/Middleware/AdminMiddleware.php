<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('api')->user()->hasRole('admin')) {
            return response()->json(Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', ''), HttpStatusCode::UNAUTHORIZED);
        }
        return $next($request);
    }
}
