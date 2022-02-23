<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;

class ManageHotelMiddleware
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
        if(!Auth::guard('api')->user()->hasPermission('manage_hotel')){
            return response()->json(Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', ''), HttpStatusCode::UNAUTHORIZED);
        }
        return $next($request);
    }
}
