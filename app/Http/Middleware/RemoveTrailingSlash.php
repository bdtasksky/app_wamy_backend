<?php

namespace App\Http\Middleware;

use Closure;

class RemoveTrailingSlash
{
    public function handle($request, Closure $next)
    {
        $path = $request->getPathInfo();

        // If the URL has a trailing slash (but isn't just "/")
        if ($path !== '/' && substr($path, -1) === '/') {
            $newPath = rtrim($path, '/');
            return redirect($newPath, 301); // Permanent redirect
        }

        return $next($request);
    }
}
