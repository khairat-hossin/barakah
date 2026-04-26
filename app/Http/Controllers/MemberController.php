<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    private const STATUSES = [
        'active',
        'inactive',
        'suspended',
    ];

    public function index(): View
    {
        $members = Member::query()
            ->withSum('savingsEntries', 'amount')
            ->latest()
            ->get();

        $statusCounts = array_merge(
            array_fill_keys(self::STATUSES, 0),
            $members->countBy('status')->all(),
        );

        return view('members.index', [
            'members' => $members,
            'statusCounts' => $statusCounts,
            'totalSaved' => $members->sum(fn (Member $member): float => (float) ($member->savings_entries_sum_amount ?? 0)),
        ]);
    }

    public function create(): View
    {
        return view('members.create', [
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['nullable', 'string', 'max:50', 'unique:members,member_code'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
            'monthly_saving_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Member::create($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Member added successfully.');
    }
}
