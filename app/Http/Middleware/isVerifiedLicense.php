<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isVerifiedLicense
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route() ? $request->route()->getName() : null;
        $allowedRoute = ['setting.install_app', 'settings.install_app', 'connectDB'];
        $isInstalled = config('app.installed', false);

        if (!in_array($routeName, $allowedRoute, true) && !$isInstalled) {
            return redirect()->route('setting.install_app');
        }

        return $next($request);
    }
}
