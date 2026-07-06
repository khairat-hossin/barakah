<?php

namespace App\Http\Controllers;

use App\Models\OrganizationProfile;
use App\Support\Branding;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Public-facing homepage / brand landing page for Echo of Unity.
     * Static, story-driven content — no live data, metrics, or dashboard cards.
     */
    public function index(): View
    {
        $org = OrganizationProfile::first();

        $address = collect([
            $org?->address_line, $org?->village_area, $org?->post_office,
            $org?->upazila, $org?->district,
        ])->filter()->implode(', ');

        return view('landing.index', [
            'org' => $org,
            'brandName' => $org?->organization_name_en ?: Branding::name(),
            'brandNameBn' => $org?->organization_name_bn,
            'motto' => $org?->motto ?: 'Friendship · Growth · Strength',
            'address' => $address,
            'phone' => $org?->mobile_number,
            'email' => $org?->email,
            'logo' => Branding::logoUrl() ?? asset('assets/logo/logo-icon.png'),
        ]);
    }
}
