<?php

namespace App\Http\Controllers;

use App\Models\ExecutiveCommittee;
use App\Models\Member;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExecutiveCommitteeController extends Controller
{
    public function index(): View
    {
        $committee = ExecutiveCommittee::with('member')
            ->whereNull('end_date')
            ->orderBy('position')
            ->get()
            ->groupBy('position');

        $allPositions = ExecutiveCommittee::POSITIONS;

        return view('executive-committee.index', [
            'committee' => $committee,
            'allPositions' => $allPositions,
        ]);
    }

    public function assign(): View
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $positions = ExecutiveCommittee::POSITIONS;

        return view('executive-committee.assign', [
            'members' => $members,
            'positions' => $positions,
            'exclusivePositions' => ExecutiveCommittee::EXCLUSIVE_POSITIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'position' => ['required', 'in:' . implode(',', array_keys(ExecutiveCommittee::POSITIONS))],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $position = $validated['position'];

        // Check if exclusive position already assigned
        if (in_array($position, ExecutiveCommittee::EXCLUSIVE_POSITIONS)) {
            $existing = ExecutiveCommittee::where('position', $position)
                ->whereNull('end_date')
                ->first();

            if ($existing) {
                return back()->with('error', 'This position is already held by ' . $existing->member->name);
            }
        }

        $member = Member::find($validated['member_id']);

        ExecutiveCommittee::create([
            'member_id' => $validated['member_id'],
            'position' => $position,
            'start_date' => $validated['start_date'],
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'position_assigned',
            'entity_type' => 'ExecutiveCommittee',
            'entity_id' => $member->id,
            'new_value' => ['position' => $position, 'member' => $member->name],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('executive-committee.index')
            ->with('success', "{$member->name} assigned as " . ucwords(str_replace('_', ' ', $position)));
    }

    public function edit(ExecutiveCommittee $committee): View
    {
        return view('executive-committee.edit', [
            'committee' => $committee,
            'positions' => ExecutiveCommittee::POSITIONS,
        ]);
    }

    public function update(Request $request, ExecutiveCommittee $committee): RedirectResponse
    {
        $validated = $request->validate([
            'position' => ['required', 'in:' . implode(',', array_keys(ExecutiveCommittee::POSITIONS))],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $newPosition = $validated['position'];

        // If changing to exclusive position, verify not assigned elsewhere
        if ($newPosition !== $committee->position && in_array($newPosition, ExecutiveCommittee::EXCLUSIVE_POSITIONS)) {
            $existing = ExecutiveCommittee::where('position', $newPosition)
                ->where('id', '!=', $committee->id)
                ->whereNull('end_date')
                ->first();

            if ($existing) {
                return back()->with('error', 'This position is already held by ' . $existing->member->name);
            }
        }

        $committee->update($validated);

        return back()->with('success', 'Committee position updated');
    }

    public function endPosition(Request $request, ExecutiveCommittee $committee): RedirectResponse
    {
        $validated = $request->validate([
            'end_date' => ['required', 'date', 'after_or_equal:' . $committee->start_date->toDateString()],
        ]);

        $committee->update([
            'end_date' => $validated['end_date'],
            'status' => 'inactive',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'position_ended',
            'entity_type' => 'ExecutiveCommittee',
            'entity_id' => $committee->id,
            'new_value' => ['end_date' => $validated['end_date']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Position ended');
    }

    public function destroy(ExecutiveCommittee $committee): RedirectResponse
    {
        $committee->delete();

        return back()->with('success', 'Committee position removed');
    }
}
