<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\OrganizationProfile;

class EnsureOrganizationSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $org = OrganizationProfile::first();

        // If organization not set up and not on setup route, redirect to setup
        if (!$org || empty($org->organization_name_en)) {
            if (!$request->routeIs('setup.*')) {
                return redirect()->route('setup.form');
            }
        } else {
            // If organization is set up and trying to access setup, redirect to dashboard
            if ($request->routeIs('setup.*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
