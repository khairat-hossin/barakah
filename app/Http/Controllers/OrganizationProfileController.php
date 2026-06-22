<?php

namespace App\Http\Controllers;

use App\Models\OrganizationProfile;
use App\Models\OrganizationProfileAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class OrganizationProfileController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = OrganizationProfile::first();

        if (!$profile) {
            return redirect()->route('organization-profile.create');
        }

        return view('organization-profile.show', ['profile' => $profile]);
    }

    public function create(): View
    {
        if (OrganizationProfile::exists()) {
            return redirect()->route('organization-profile.edit', OrganizationProfile::first())
                ->with('info', 'Organization profile already exists. Edit it instead.');
        }

        return view('organization-profile.create');
    }

    public function store(Request $request)
    {
        if (OrganizationProfile::exists()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Organization profile already exists.'], 422);
            }
            return back()->with('error', 'Organization profile already exists.');
        }

        try {
            $validated = $this->validateProfilePartial($request);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $profile = OrganizationProfile::create($validated);

        $this->logAudit($profile, 'created', 'Organization Profile Created', null, $validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'profile_id' => $profile->id], 201);
        }

        return redirect()->route('organization-profile.show', $profile)
            ->with('success', 'Organization profile created successfully');
    }

    public function show(OrganizationProfile $organizationProfile): View
    {
        return view('organization-profile.show', ['profile' => $organizationProfile]);
    }

    public function edit(OrganizationProfile $organizationProfile): View
    {
        return view('organization-profile.edit', ['profile' => $organizationProfile]);
    }

    public function update(Request $request, OrganizationProfile $organizationProfile)
    {
        try {
            $validated = $this->validateProfilePartial($request);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $validated['updated_by'] = auth()->id();

        $oldValues = $organizationProfile->getAttributes();
        $organizationProfile->update($validated);

        foreach ($validated as $key => $value) {
            if (($oldValues[$key] ?? null) !== $value) {
                $this->logAudit(
                    $organizationProfile,
                    'updated',
                    $this->getSectionName($key),
                    $oldValues[$key] ?? null,
                    $value,
                    $key
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('organization-profile.show', $organizationProfile)
            ->with('success', 'Organization profile updated successfully');
    }

    public function destroy(OrganizationProfile $organizationProfile): RedirectResponse
    {
        $this->authorize('delete', $organizationProfile);

        $organizationProfile->delete();

        return redirect()->route('organization-profile.index')
            ->with('success', 'Organization profile deleted');
    }

    public function editSection(Request $request, OrganizationProfile $organizationProfile, string $section): View
    {
        return view("organization-profile.sections.{$section}", ['profile' => $organizationProfile]);
    }

    public function updateSection(Request $request, OrganizationProfile $organizationProfile, string $section): RedirectResponse
    {
        $sectionData = $this->validateSection($request, $section);
        $oldValues = $organizationProfile->only(array_keys($sectionData));

        $organizationProfile->update($sectionData);

        foreach ($sectionData as $key => $value) {
            if (($oldValues[$key] ?? null) !== $value) {
                $this->logAudit(
                    $organizationProfile,
                    'section_updated',
                    ucwords(str_replace('_', ' ', $section)),
                    $oldValues[$key] ?? null,
                    $value,
                    $key
                );
            }
        }

        return back()->with('success', ucfirst($section) . ' updated successfully');
    }

    public function auditLogs(OrganizationProfile $organizationProfile): View
    {
        $logs = $organizationProfile->auditLogs()
            ->orderByDesc('timestamp')
            ->paginate(20);

        return view('organization-profile.audit-logs', [
            'profile' => $organizationProfile,
            'logs' => $logs,
        ]);
    }

    private function validateProfilePartial(Request $request): array
    {
        $rules = [
            // General Information
            'organization_name_bn' => ['nullable', 'string', 'max:255'],
            'organization_name_en' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'logo_path' => ['nullable', 'string'],
            'seal_path' => ['nullable', 'string'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'organization_type' => ['nullable', 'in:coop,ngo,mutual,association,other'],
            'status' => ['nullable', 'in:active,inactive,suspended'],

            // Contact
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'secondary_mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook_page' => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],

            // Address
            'address_line' => ['nullable', 'string'],
            'village_area' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'union_ward' => ['nullable', 'string', 'max:255'],
            'upazila' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            // Mission & Objectives
            'motto' => ['nullable', 'string', 'max:255'],
            'vision_statement' => ['nullable', 'string'],
            'mission_statement' => ['nullable', 'string'],
            'about_organization' => ['nullable', 'string'],
            'core_values' => ['nullable', 'array'],
            'objectives' => ['nullable', 'array'],

            // Share Structure
            'total_shares' => ['nullable', 'integer', 'min:1'],
            'share_face_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'share_ownership_model' => ['nullable', 'in:individual,collective,hybrid'],
            'share_transfer_allowed' => ['nullable', 'boolean'],
            'share_transfer_rules' => ['nullable', 'string'],
            'minimum_shares_per_member' => ['nullable', 'integer', 'min:0'],
            'maximum_shares_per_member' => ['nullable', 'integer', 'min:0'],

            // Membership Rules
            'membership_type' => ['nullable', 'in:share_based,open,invitation_only'],
            'minimum_share_requirement' => ['nullable', 'integer', 'min:0'],
            'new_member_admission_allowed' => ['nullable', 'boolean'],

            // Committee Structure
            'committee_term_length' => ['nullable', 'integer', 'min:1'],
            'maximum_consecutive_terms' => ['nullable', 'integer', 'min:1'],
            'election_required' => ['nullable', 'boolean'],
            're_election_allowed' => ['nullable', 'boolean'],
            'committee_positions' => ['nullable', 'array'],

            // Financial
            'default_currency' => ['nullable', 'string', 'max:3'],
            'reserve_fund_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'emergency_fund_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:20'],

            // Contributions
            'membership_fee' => ['nullable', 'numeric', 'min:0'],
            'share_purchase_fee' => ['nullable', 'numeric', 'min:0'],
            'annual_contribution' => ['nullable', 'numeric', 'min:0'],
            'special_contribution' => ['nullable', 'numeric', 'min:0'],

            // Meeting Rules
            'general_meeting_notice_days' => ['nullable', 'integer', 'min:1'],
            'general_meeting_quorum_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'general_meeting_voting_method' => ['nullable', 'in:majority,unanimous,consensus'],
            'committee_meeting_notice_days' => ['nullable', 'integer', 'min:1'],
            'committee_meeting_quorum_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'minimum_committee_meetings_per_year' => ['nullable', 'integer', 'min:1'],
        ];

        return $request->validate($rules);
    }

    private function validateProfile(Request $request, int $id = null): array
    {
        return $request->validate([
            // General Information
            'organization_name_bn' => ['required', 'string', 'max:255'],
            'organization_name_en' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:50'],
            'logo_path' => ['nullable', 'string'],
            'seal_path' => ['nullable', 'string'],
            'registration_number' => ['required', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'organization_type' => ['required', 'in:coop,ngo,mutual,association,other'],
            'status' => ['required', 'in:active,inactive,suspended'],

            // Contact
            'mobile_number' => ['required', 'string', 'max:20'],
            'secondary_mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook_page' => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],

            // Address
            'address_line' => ['required', 'string'],
            'village_area' => ['required', 'string', 'max:255'],
            'post_office' => ['required', 'string', 'max:255'],
            'union_ward' => ['required', 'string', 'max:255'],
            'upazila' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:100'],

            // Mission & Objectives
            'motto' => ['required', 'string', 'max:255'],
            'vision_statement' => ['required', 'string'],
            'mission_statement' => ['required', 'string'],
            'core_values' => ['nullable', 'array'],
            'objectives' => ['nullable', 'array'],
            'about_organization' => ['nullable', 'string'],

            // Share Structure
            'total_shares' => ['required', 'integer', 'min:1'],
            'share_face_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'share_ownership_model' => ['required', 'in:individual,collective,hybrid'],
            'share_transfer_allowed' => ['boolean'],
            'partial_share_transfer_allowed' => ['boolean'],
            'minimum_shares_per_member' => ['required', 'integer', 'min:1'],
            'maximum_shares_per_member' => ['nullable', 'integer', 'min:1'],

            // Membership Rules
            'membership_type' => ['required', 'in:share_based,open,invitation_only'],
            'new_member_admission_allowed' => ['boolean'],
            'membership_approval_required' => ['boolean'],
            'minimum_share_requirement' => ['required', 'integer', 'min:0'],
            'maximum_share_ownership' => ['nullable', 'integer', 'min:0'],
            'allow_membership_transfer' => ['boolean'],

            // Committee
            'committee_term_length' => ['required', 'integer', 'min:1'],
            'maximum_consecutive_terms' => ['required', 'integer', 'min:1'],
            'election_required' => ['boolean'],
            're_election_allowed' => ['boolean'],
            'committee_positions' => ['nullable', 'array'],

            // Financial
            'default_currency' => ['required', 'string', 'max:3'],
            'reserve_fund_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'emergency_fund_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:20'],

            // Contributions
            'membership_fee' => ['nullable', 'numeric', 'min:0'],
            'share_purchase_fee' => ['nullable', 'numeric', 'min:0'],
            'annual_contribution' => ['nullable', 'numeric', 'min:0'],
            'special_contribution' => ['nullable', 'numeric', 'min:0'],

            // Meeting Rules
            'general_meeting_notice_days' => ['required', 'integer', 'min:1'],
            'general_meeting_quorum_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'general_meeting_voting_method' => ['required', 'in:majority,unanimous,consensus'],
            'committee_meeting_notice_days' => ['required', 'integer', 'min:1'],
            'committee_meeting_quorum_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'minimum_committee_meetings_per_year' => ['required', 'integer', 'min:1'],
        ]);
    }

    private function validateSection(Request $request, string $section): array
    {
        return match ($section) {
            'general' => $request->validate([
                'organization_name_bn' => ['required', 'string', 'max:255'],
                'organization_name_en' => ['required', 'string', 'max:255'],
                'short_name' => ['required', 'string', 'max:50'],
                'registration_number' => ['required', 'string', 'max:100'],
                'registration_date' => ['nullable', 'date'],
                'organization_type' => ['required', 'in:coop,ngo,mutual,association,other'],
                'status' => ['required', 'in:active,inactive,suspended'],
                'mobile_number' => ['required', 'string', 'max:20'],
                'secondary_mobile' => ['nullable', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
                'facebook_page' => ['nullable', 'url', 'max:255'],
                'whatsapp_number' => ['nullable', 'string', 'max:20'],
            ]),
            'address' => $request->validate([
                'address_line' => ['required', 'string'],
                'village_area' => ['required', 'string', 'max:255'],
                'post_office' => ['required', 'string', 'max:255'],
                'union_ward' => ['required', 'string', 'max:255'],
                'upazila' => ['required', 'string', 'max:255'],
                'district' => ['required', 'string', 'max:255'],
                'division' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['required', 'string', 'max:10'],
                'country' => ['required', 'string', 'max:100'],
            ]),
            'mission' => $request->validate([
                'motto' => ['required', 'string', 'max:255'],
                'vision_statement' => ['required', 'string'],
                'mission_statement' => ['required', 'string'],
                'core_values' => ['nullable', 'array'],
                'objectives' => ['nullable', 'array'],
                'about_organization' => ['nullable', 'string'],
            ]),
            'shares' => $request->validate([
                'total_shares' => ['required', 'integer', 'min:1'],
                'share_face_value' => ['required', 'numeric', 'min:0'],
                'currency' => ['required', 'string', 'max:3'],
                'share_ownership_model' => ['required', 'in:individual,collective,hybrid'],
                'share_transfer_allowed' => ['boolean'],
                'partial_share_transfer_allowed' => ['boolean'],
                'minimum_shares_per_member' => ['required', 'integer', 'min:1'],
                'maximum_shares_per_member' => ['nullable', 'integer', 'min:1'],
            ]),
            'membership' => $request->validate([
                'membership_type' => ['required', 'in:share_based,open,invitation_only'],
                'new_member_admission_allowed' => ['boolean'],
                'membership_approval_required' => ['boolean'],
                'minimum_share_requirement' => ['required', 'integer', 'min:0'],
                'maximum_share_ownership' => ['nullable', 'integer', 'min:0'],
                'allow_membership_transfer' => ['boolean'],
            ]),
            'committee' => $request->validate([
                'committee_term_length' => ['required', 'integer', 'min:1'],
                'maximum_consecutive_terms' => ['required', 'integer', 'min:1'],
                'election_required' => ['boolean'],
                're_election_allowed' => ['boolean'],
                'committee_positions' => ['nullable', 'array'],
            ]),
            'financial' => $request->validate([
                'default_currency' => ['required', 'string', 'max:3'],
                'reserve_fund_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
                'emergency_fund_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
                'bank_name' => ['nullable', 'string', 'max:255'],
                'branch_name' => ['nullable', 'string', 'max:255'],
                'account_name' => ['nullable', 'string', 'max:255'],
                'account_number' => ['nullable', 'string', 'max:50'],
                'routing_number' => ['nullable', 'string', 'max:50'],
                'swift_code' => ['nullable', 'string', 'max:20'],
                'membership_fee' => ['nullable', 'numeric', 'min:0'],
                'share_purchase_fee' => ['nullable', 'numeric', 'min:0'],
                'annual_contribution' => ['nullable', 'numeric', 'min:0'],
                'special_contribution' => ['nullable', 'numeric', 'min:0'],
            ]),
            'meetings' => $request->validate([
                'general_meeting_notice_days' => ['required', 'integer', 'min:1'],
                'general_meeting_quorum_percentage' => ['required', 'integer', 'min:1', 'max:100'],
                'general_meeting_voting_method' => ['required', 'in:majority,unanimous,consensus'],
                'committee_meeting_notice_days' => ['required', 'integer', 'min:1'],
                'committee_meeting_quorum_percentage' => ['required', 'integer', 'min:1', 'max:100'],
                'minimum_committee_meetings_per_year' => ['required', 'integer', 'min:1'],
            ]),
            default => []
        };
    }

    private function logAudit(OrganizationProfile $profile, string $action, string $section, $oldValue = null, $newValue = null, string $field = null): void
    {
        OrganizationProfileAuditLog::create([
            'organization_profile_id' => $profile->id,
            'changed_by' => auth()->id(),
            'action_type' => $action,
            'section_name' => $section,
            'field_name' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function getSectionName(string $fieldName): string
    {
        return match (true) {
            str_starts_with($fieldName, 'organization_') || str_starts_with($fieldName, 'mobile_') || 
            str_starts_with($fieldName, 'secondary_') || str_starts_with($fieldName, 'email') || 
            str_starts_with($fieldName, 'website') || str_starts_with($fieldName, 'facebook_') || 
            str_starts_with($fieldName, 'whatsapp_') || str_starts_with($fieldName, 'address_') || 
            str_starts_with($fieldName, 'village_') || str_starts_with($fieldName, 'post_') || 
            str_starts_with($fieldName, 'union_') || str_starts_with($fieldName, 'upazila') || 
            str_starts_with($fieldName, 'district') || str_starts_with($fieldName, 'division') || 
            str_starts_with($fieldName, 'postal_') || str_starts_with($fieldName, 'country') => 'General Information',
            str_starts_with($fieldName, 'motto') || str_starts_with($fieldName, 'vision_') || 
            str_starts_with($fieldName, 'mission_') || str_starts_with($fieldName, 'core_') || 
            str_starts_with($fieldName, 'objectives') || str_starts_with($fieldName, 'about_') => 'Mission & Objectives',
            str_starts_with($fieldName, 'total_shares') || str_starts_with($fieldName, 'share_') => 'Share Structure',
            str_starts_with($fieldName, 'membership_') || str_starts_with($fieldName, 'minimum_') || 
            str_starts_with($fieldName, 'maximum_') || str_starts_with($fieldName, 'new_') || 
            str_starts_with($fieldName, 'allow_') => 'Membership Rules',
            str_starts_with($fieldName, 'committee_') || str_starts_with($fieldName, 'election_') || 
            str_starts_with($fieldName, 're_election_') => 'Committee Structure',
            str_starts_with($fieldName, 'default_') || str_starts_with($fieldName, 'reserve_') || 
            str_starts_with($fieldName, 'emergency_') || str_starts_with($fieldName, 'bank_') || 
            str_starts_with($fieldName, 'branch_') || str_starts_with($fieldName, 'account_') || 
            str_starts_with($fieldName, 'routing_') || str_starts_with($fieldName, 'swift_') => 'Financial Configuration',
            str_starts_with($fieldName, 'general_meeting_') || str_starts_with($fieldName, 'committee_meeting_') || 
            str_starts_with($fieldName, 'minimum_committee_') => 'Meeting Rules',
            default => 'Organization Profile'
        };
    }
}
