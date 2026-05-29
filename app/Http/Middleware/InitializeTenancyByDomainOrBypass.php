<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomainOrBypass
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        $isCentral = false;

        foreach ($centralDomains as $centralDomain) {
            if ($host === $centralDomain) {
                $isCentral = true;
                break;
            }

            if (str_contains($centralDomain, '*')) {
                $pattern = '/^'.str_replace(['.', '*'], ['\.', '[a-zA-Z0-9_-]+'], $centralDomain).'$/';
                if (preg_match($pattern, $host)) {
                    $isCentral = true;
                    break;
                }
            }
        }

        if ($isCentral) {
            return $next($request);
        }

        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }
}
