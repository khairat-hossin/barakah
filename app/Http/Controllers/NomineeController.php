<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nominee;
use App\Models\NomineeAuditHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class NomineeController extends Controller
{
    public function index(Member $member): View
    {
        $nominees = $member->nominees()->orderBy('is_primary', 'desc')->get();
        $totalAllocation = $nominees->sum('allocation_percentage');

        return view('nominees.index', [
            'member' => $member,
            'nominees' => $nominees,
            'totalAllocation' => $totalAllocation,
            'canAddMore' => $totalAllocation < 100,
        ]);
    }

    public function create(Member $member): View
    {
        $nominees = $member->nominees;
        $usedAllocation = $nominees->sum('allocation_percentage');

        return view('nominees.create', [
            'member' => $member,
            'usedAllocation' => $usedAllocation,
            'remainingAllocation' => 100 - $usedAllocation,
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'nid_number' => ['nullable', 'string', 'max:50', 'unique:nominees,nid_number'],
            'birth_registration' => ['nullable', 'string', 'max:50', 'unique:nominees,birth_registration'],
            'relationship' => ['required', 'in:son,daughter,wife,husband,parent,sibling,other'],
            'mobile_number' => ['nullable', 'string', 'max:20', 'unique:nominees,mobile_number'],
            'email' => ['nullable', 'email', 'max:255', 'unique:nominees,email'],
            'address' => ['nullable', 'string', 'max:1000'],
            'allocation_percentage' => ['required', 'integer', 'between:1,100'],
            'is_primary' => ['boolean'],
        ]);

        // Verify total allocation doesn't exceed 100%
        $currentAllocation = $member->nominees()->sum('allocation_percentage');
        $newTotal = $currentAllocation + $validated['allocation_percentage'];

        if ($newTotal > 100) {
            return back()->with('error', 'Total nominee allocation would exceed 100%');
        }

        DB::transaction(function () use ($member, $validated, $newTotal) {
            // If this is primary, unset other primaries
            if ($validated['is_primary'] ?? false) {
                $member->nominees()->update(['is_primary' => false]);
            }

            $nominee = $member->nominees()->create($validated);

            // Log the change
            NomineeAuditHistory::create([
                'nominee_id' => $nominee->id,
                'member_id' => $member->id,
                'action' => 'created',
                'full_name' => $nominee->full_name,
                'allocation_percentage' => $nominee->allocation_percentage,
                'total_allocation_after_change' => $newTotal,
                'changed_by' => auth()->id(),
                'timestamp' => now(),
            ]);
        });

        return redirect()->route('nominees.index', $member)
            ->with('success', 'Nominee added successfully');
    }

    public function edit(Member $member, Nominee $nominee): View
    {
        $this->authorize('update', $nominee);

        $currentAllocation = $member->nominees()
            ->where('id', '!=', $nominee->id)
            ->sum('allocation_percentage');

        return view('nominees.edit', [
            'member' => $member,
            'nominee' => $nominee,
            'usedAllocation' => $currentAllocation,
            'remainingAllocation' => 100 - $currentAllocation,
        ]);
    }

    public function update(Request $request, Member $member, Nominee $nominee): RedirectResponse
    {
        $this->authorize('update', $nominee);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'nid_number' => ['nullable', 'string', 'max:50', 'unique:nominees,nid_number,'.$nominee->id],
            'birth_registration' => ['nullable', 'string', 'max:50', 'unique:nominees,birth_registration,'.$nominee->id],
            'relationship' => ['required', 'in:son,daughter,wife,husband,parent,sibling,other'],
            'mobile_number' => ['nullable', 'string', 'max:20', 'unique:nominees,mobile_number,'.$nominee->id],
            'email' => ['nullable', 'email', 'max:255', 'unique:nominees,email,'.$nominee->id],
            'address' => ['nullable', 'string', 'max:1000'],
            'allocation_percentage' => ['required', 'integer', 'between:1,100'],
            'is_primary' => ['boolean'],
        ]);

        // Verify total allocation doesn't exceed 100%
        $otherAllocation = $member->nominees()
            ->where('id', '!=', $nominee->id)
            ->sum('allocation_percentage');
        $newTotal = $otherAllocation + $validated['allocation_percentage'];

        if ($newTotal > 100) {
            return back()->with('error', 'Total nominee allocation would exceed 100%');
        }

        DB::transaction(function () use ($nominee, $member, $validated, $newTotal) {
            $oldAllocation = $nominee->allocation_percentage;
            $nominee->update($validated);

            if ($validated['is_primary'] ?? false) {
                $member->nominees()->where('id', '!=', $nominee->id)->update(['is_primary' => false]);
            }

            NomineeAuditHistory::create([
                'nominee_id' => $nominee->id,
                'member_id' => $member->id,
                'action' => 'updated',
                'full_name' => $nominee->full_name,
                'allocation_percentage' => $nominee->allocation_percentage,
                'total_allocation_after_change' => $newTotal,
                'changed_by' => auth()->id(),
                'timestamp' => now(),
            ]);
        });

        return redirect()->route('nominees.index', $member)
            ->with('success', 'Nominee updated successfully');
    }

    public function destroy(Member $member, Nominee $nominee): RedirectResponse
    {
        $this->authorize('delete', $nominee);

        DB::transaction(function () use ($nominee, $member) {
            $newTotal = $member->nominees()
                ->where('id', '!=', $nominee->id)
                ->sum('allocation_percentage');

            NomineeAuditHistory::create([
                'nominee_id' => $nominee->id,
                'member_id' => $member->id,
                'action' => 'deleted',
                'full_name' => $nominee->full_name,
                'allocation_percentage' => $nominee->allocation_percentage,
                'total_allocation_after_change' => $newTotal,
                'changed_by' => auth()->id(),
                'timestamp' => now(),
            ]);

            $nominee->delete();
        });

        return redirect()->route('nominees.index', $member)
            ->with('success', 'Nominee removed');
    }

    public function setPrimary(Member $member, Nominee $nominee): RedirectResponse
    {
        $this->authorize('update', $nominee);

        $member->nominees()->update(['is_primary' => false]);
        $nominee->update(['is_primary' => true]);

        return back()->with('success', 'Primary nominee updated');
    }
}
