<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Multitenancy\Multitenancy;
use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Spatie\Permission\PermissionRegistrar;

class InitializeTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var TenantFinder $tenantFinder */
        $tenantFinder = app(config('multitenancy.tenant_finder'));

        $tenant = $tenantFinder->findForRequest($request);
        if ($tenant) {
            config(['database.default' => 'tenant']);
            $tenant->makeCurrent();

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $next($request);
    }
}
