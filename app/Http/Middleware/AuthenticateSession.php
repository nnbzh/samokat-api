<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponder;
use App\Models\Session;
use Closure;
use Illuminate\Http\Request;

class AuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! $header = $request->headers->get('x-api-session')) {
            return ApiResponder::fail('Неавторизованный', 401);
        }

        $session = Session::query()->where('session', $header)->first();

        if ($session && $session->isValid()) {
            return $next($request);
        }

        return ApiResponder::fail('Неавторизованный', 401);
    }
}
