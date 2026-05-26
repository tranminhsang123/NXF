<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== 'admin') {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (! $routeName || ! str_starts_with($routeName, 'admin.')) {
            return $next($request);
        }

        $map = config('admin_route_permissions.routes', []);
        $required = is_array($map) ? ($map[$routeName] ?? null) : null;

        if ($required === null) {
            if ($user->adminBypassesRoutePermissionMap()) {
                return $next($request);
            }
            abort(403, 'Route admin chưa được gán quyền. Liên hệ super admin.');
        }

        if ($user->hasAdminPermission($required)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền thực hiện thao tác này.');
    }
}
