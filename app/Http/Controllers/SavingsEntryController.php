<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SavingsEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavingsEntryController extends Controller
{
    private const PAYMENT_METHODS = [
        'cash',
        'bank',
        'mobile_banking',
        'check',
        'other',
    ];

    public function index(): View
    {
        $entries = SavingsEntry::query()
            ->with(['member', 'recorder'])
            ->latest('deposit_date')
            ->latest()
            ->get();

        return view('savings.index', [
            'entries' => $entries,
            'totalCollected' => $entries->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
            'monthlyCollected' => $entries
                ->filter(fn (SavingsEntry $entry): bool => optional($entry->deposit_date)->isCurrentMonth() === true)
                ->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
        ]);
    }

    public function create(): View
    {
        return view('savings.create', [
            'members' => Member::query()->where('status', 'active')->orderBy('name')->get(),
            'paymentMethods' => self::PAYMENT_METHODS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'contribution_month' => ['nullable', 'date'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', self::PAYMENT_METHODS)],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['recorded_by'] = $request->user()->id;

        SavingsEntry::create($validated);

        return redirect()
            ->route('savings.index')
            ->with('success', 'Savings entry recorded successfully.');
    }
}
