<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\SavingsEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavingsEntryController extends Controller
{
    public function index(): View
    {
        $entries = SavingsEntry::query()
            ->with(['member', 'recorder'])
            ->latest('deposit_date')
            ->latest()
            ->get();

        return view('deposits.index', [
            'entries' => $entries,
            'totalCollected' => $entries->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
            'monthlyCollected' => $entries
                ->filter(fn (SavingsEntry $entry): bool => optional($entry->deposit_date)->isCurrentMonth() === true)
                ->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
        ]);
    }

    public function create(): View
    {
        return view('deposits.create', [
            'members' => Member::query()->where('status', 'active')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $paymentMethodIds = PaymentMethod::active()->pluck('id')->toArray();

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'months' => ['required', 'array', 'min:1'],
            'months.*' => ['string', 'regex:/^\d{1,2}\/\d{4}$/'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $months = $validated['months'];
        unset($validated['months']);

        $validated['recorded_by'] = $request->user()->id;

        // Create the savings entry
        $savingsEntry = SavingsEntry::create($validated);

        // Create monthly deposit records
        foreach ($months as $monthYear) {
            [$month, $year] = explode('/', $monthYear);
            \App\Models\MemberDepositMonth::create([
                'member_id' => $validated['member_id'],
                'month' => (int)$month,
                'year' => (int)$year,
                'savings_entry_id' => $savingsEntry->id,
            ]);
        }

        return redirect()
            ->route('deposits.index')
            ->with('success', 'Deposit recorded successfully for ' . count($months) . ' month(s).');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $paymentMethod = $request->get('payment_method', '');

        $query = SavingsEntry::with(['member', 'recorder']);

        if ($search) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%");
            });
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $filtered = $query->count();
        $total = SavingsEntry::count();

        $entries = $query->latest('deposit_date')
            ->latest()
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $entries->map(function (SavingsEntry $entry) {
            return [
                'id' => $entry->id,
                'member_id' => $entry->member_id,
                'member_name' => $entry->member->name ?? 'N/A',
                'amount' => $entry->amount,
                'deposit_date' => $entry->deposit_date,
                'payment_method' => $entry->payment_method,
                'transaction_id' => $entry->transaction_id ?? '',
                'recorder_name' => $entry->recorder->name ?? 'System',
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function show(SavingsEntry $savingsEntry): View
    {
        $savingsEntry->load(['member', 'recorder']);

        return view('deposits.show', [
            'deposit' => $savingsEntry,
        ]);
    }

    public function edit(SavingsEntry $savingsEntry): View
    {
        return view('deposits.edit', [
            'deposit' => $savingsEntry,
            'members' => Member::query()->where('status', 'active')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function update(Request $request, SavingsEntry $savingsEntry): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $savingsEntry->update($validated);

        return redirect()
            ->route('deposits.show', $savingsEntry)
            ->with('success', 'Deposit updated successfully.');
    }

    public function destroy(SavingsEntry $savingsEntry): RedirectResponse
    {
        $savingsEntry->delete();

        return redirect()
            ->route('deposits.index')
            ->with('success', 'Deposit deleted successfully.');
    }

    public function quickStore(Request $request)
    {
        $this->authorize('create', SavingsEntry::class);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_id' => ['required', 'string', 'max:100', 'unique:savings_entries'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        $savingsEntry = SavingsEntry::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'deposit_date' => $monthDate->endOfMonth(),
            'payment_method_id' => $validated['payment_method_id'],
            'transaction_id' => $validated['transaction_id'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        // Record the month as paid
        \App\Models\MemberDepositMonth::updateOrCreate(
            [
                'member_id' => $validated['member_id'],
                'month' => $monthDate->month,
                'year' => $monthDate->year,
            ],
            ['deposit_date' => now()]
        );

        return response()->json([
            'message' => 'Deposit recorded successfully',
            'deposit' => $savingsEntry,
        ], 201);
    }
}
