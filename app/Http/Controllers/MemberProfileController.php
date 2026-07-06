<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Helpers\ShareHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    /**
     * A member may only reach their OWN profile; staff (with "view members")
     * may reach anyone's. These routes are otherwise auth-only.
     */
    private function authorizeSelfOrStaff(Member $member): void
    {
        $user = auth()->user();

        abort_unless(
            $user->can('view members') || (int) $member->user_id === (int) $user->id,
            403,
            'You can only access your own member profile.'
        );
    }

    public function show(Member $member): View
    {
        $this->authorizeSelfOrStaff($member);

        $member->load([
            'shares.share',
            'nominees',
            'currentPosition',
            'documents',
            'shareOwnershipHistory.share',
        ]);

        return view('member-profiles.show', [
            'member' => $member,
            'totalShares' => $member->totalSharesOwned(),
            'nomineeAllocation' => $member->nomineeAllocationPercentage(),
            'emiPerMonth' => ShareHelper::calculateEmiPerMonth($member->id),
        ]);
    }

    public function edit(Member $member): View
    {
        $this->authorizeSelfOrStaff($member);

        return view('member-profiles.edit', [
            'member' => $member,
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $this->authorizeSelfOrStaff($member);

        $validated = $request->validate([
            'name_bn' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'nid_number' => ['required', 'string', 'max:50', 'unique:members,nid_number,' . $member->id],
            'birth_registration' => ['nullable', 'string', 'max:50', 'unique:members,birth_registration,' . $member->id],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number,' . $member->id],
            'tax_id' => ['nullable', 'string', 'max:50', 'unique:members,tax_id,' . $member->id],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,' . $member->id],
            'phone' => ['required', 'string', 'max:20', 'unique:members,phone,' . $member->id],
            'secondary_mobile' => ['nullable', 'string', 'max:20', 'unique:members,secondary_mobile,' . $member->id],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'unique:members,whatsapp_number,' . $member->id],
            'present_address_village' => ['nullable', 'string', 'max:255'],
            'present_address_po' => ['nullable', 'string', 'max:255'],
            'present_address_union' => ['nullable', 'string', 'max:255'],
            'present_address_upazila' => ['nullable', 'string', 'max:255'],
            'present_address_district' => ['nullable', 'string', 'max:255'],
            'present_address_postal' => ['nullable', 'string', 'max:10'],
            'same_as_permanent' => ['boolean'],
            'permanent_address_village' => ['required', 'string', 'max:255'],
            'permanent_address_po' => ['required', 'string', 'max:255'],
            'permanent_address_union' => ['nullable', 'string', 'max:255'],
            'permanent_address_upazila' => ['required', 'string', 'max:255'],
            'permanent_address_district' => ['required', 'string', 'max:255'],
            'permanent_address_postal' => ['required', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'trade_license_number' => ['nullable', 'string', 'max:100'],
            'office_designation' => ['nullable', 'string', 'max:255'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:1000'],
        ]);

        // When "same as permanent" is set, mirror the permanent address into present.
        if ($validated['same_as_permanent'] ?? false) {
            $validated['present_address_village'] = $validated['permanent_address_village'];
            $validated['present_address_po'] = $validated['permanent_address_po'];
            $validated['present_address_union'] = $validated['permanent_address_union'] ?? null;
            $validated['present_address_upazila'] = $validated['permanent_address_upazila'];
            $validated['present_address_district'] = $validated['permanent_address_district'];
            $validated['present_address_postal'] = $validated['permanent_address_postal'];
        }

        $member->update($validated);

        return redirect()->route('member-profiles.show', $member)
            ->with('success', 'Member profile updated successfully');
    }

    public function exportPdf(Member $member)
    {
        $this->authorizeSelfOrStaff($member);

        $member->load(['nominees', 'currentPosition', 'documents']);

        // For now, return a simple response
        // In production, would use a PDF library like dompdf
        return view('member-profiles.pdf', [
            'member' => $member,
        ]);
    }
}
