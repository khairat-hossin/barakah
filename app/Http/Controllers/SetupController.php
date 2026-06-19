<?php

namespace App\Http\Controllers;

use App\Models\OrganizationProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function form(): View
    {
        $org = OrganizationProfile::first();

        return view('setup.form', [
            'org' => $org,
            'organizationTypes' => [
                'coop' => 'Cooperative',
                'ngo' => 'NGO',
                'mutual' => 'Mutual Organization',
                'association' => 'Association',
                'other' => 'Other',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name_en' => ['required', 'string', 'max:255'],
            'organization_name_bn' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'organization_type' => ['required', 'in:coop,ngo,mutual,association,other'],
            'email' => ['required', 'email', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'secondary_mobile' => ['nullable', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:500'],
            'village_area' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'union_ward' => ['nullable', 'string', 'max:255'],
            'upazila' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'vision_statement' => ['nullable', 'string'],
            'mission_statement' => ['nullable', 'string'],
            'motto' => ['nullable', 'string', 'max:255'],
            'share_face_value' => ['required', 'numeric', 'min:0'],
            'total_shares' => ['required', 'integer', 'min:0'],
            'membership_fee' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
        ]);

        $org = OrganizationProfile::firstOrCreate(
            [],
            $validated
        );

        if ($org->wasRecentlyCreated) {
            return redirect()->route('dashboard')->with('success', 'Organization setup completed successfully!');
        } else {
            $org->update($validated);
            return redirect()->route('dashboard')->with('success', 'Organization information updated successfully!');
        }
    }
}
