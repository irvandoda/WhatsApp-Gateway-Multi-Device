<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnsureUserMediaFolder
{
    public function handle($request, Closure $next)
    {
        $is_installed = env('APP_INSTALLED', false);
        
        if ($is_installed === 'false' || $is_installed === false) {
            return $next($request);
        }
        
        if (Auth::check()) {
            $path = 'files/'.Auth::id();
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
        }
        return $next($request);
    }
}
