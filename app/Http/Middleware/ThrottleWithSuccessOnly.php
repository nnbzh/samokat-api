<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponder;
use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleWithSuccessOnly
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $key, $maxAttempts, $decayMinutes)
    {
        $response = $next($request);

        if ($response->status() === Response::HTTP_OK) {
            $this->limiter->hit($key, $decayMinutes * 60);
        }

        $remainingAttempts = $this->limiter->retriesLeft($key, $maxAttempts);

        $response->headers->add([
            'X-RateLimit-Limit'     => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);

        if ($remainingAttempts < 0) {
            ApiResponder::fail('Попробуйте позже', 429);
        }

        return $response;
    }
}
