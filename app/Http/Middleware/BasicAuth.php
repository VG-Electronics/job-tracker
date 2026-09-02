<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('up')) {
            return $next($request);
        }

        $login = config('services.basic_auth.login');
        $password = config('services.basic_auth.password');

        if (blank($login) || blank($password)) {
            return $next($request);
        }

        if ($request->getUser() === $login && $request->getPassword() === $password) {
            return $next($request);
        }

        return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
    }
}
