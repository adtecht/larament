<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Models\Tenant\TenantUser;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class InitializeTenancyBySession
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant_id = $request->session()->get('tenant_id');

        if ($tenant_id)
        {
            $tenant = Tenant::find($tenant_id);

            if ($tenant)
            {
                tenancy()->initialize($tenant);

                // after tenancy init, ensure central user is member of tenant DB
                $central_user = Auth::user();

                if ($central_user)
                {
                    $exists_in_tenant = TenantUser::query()
                        ->where('email', $central_user->email)
                        ->exists();

                    if (!$exists_in_tenant)
                    {
                        tenancy()->end();
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect()->to('/welcome');
                    }
                }
            }
            else
            {
                $request->session()->forget('tenant_id');
                return redirect()->to('/welcome');
            }
        }

        try
        {
            return $next($request);
        }
        finally
        {
            if (tenancy()->initialized)
            {
                tenancy()->end();
            }
        }
    }
}
