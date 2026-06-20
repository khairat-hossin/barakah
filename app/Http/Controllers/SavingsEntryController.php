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

        // Enforce one deposit per member per month — reject any month already paid.
        $duplicateMonths = [];
        foreach ($months as $monthYear) {
            [$month, $year] = explode('/', $monthYear);
            $exists = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
                ->where('month', (int) $month)
                ->where('year', (int) $year)
                ->exists();
            if ($exists) {
                $duplicateMonths[] = \Carbon\Carbon::createFromDate((int) $year, (int) $month, 1)->format('M Y');
            }
        }

        if (! empty($duplicateMonths)) {
            return back()
                ->withInput()
                ->withErrors([
                    'months' => 'This member already has a deposit for: ' . implode(', ', $duplicateMonths) . '. Only one deposit per month is allowed.',
                ]);
        }

        $validated['recorded_by'] = $request->user()->id;
        $validated['payment_method'] = PaymentMethod::whereKey($validated['payment_method_id'])->value('code');

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $months) {
            // Create the savings entry
            $savingsEntry = SavingsEntry::create($validated);

            // Create monthly deposit records
            foreach ($months as $monthYear) {
                [$month, $year] = explode('/', $monthYear);
                \App\Models\MemberDepositMonth::create([
                    'member_id' => $validated['member_id'],
                    'month' => (int) $month,
                    'year' => (int) $year,
                    'savings_entry_id' => $savingsEntry->id,
                ]);
            }

            return $savingsEntry;
        });

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

    public function receipt(SavingsEntry $savingsEntry)
    {
        $this->authorize('view', $savingsEntry);

        $savingsEntry->load(['member', 'recorder', 'paymentMethod']);

        return \App\Support\PdfRenderer::download(
            'deposits.receipt',
            [
                'deposit' => $savingsEntry,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
            'deposit-receipt-' . ($savingsEntry->transaction_id ?: $savingsEntry->id) . '.pdf',
            ['margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 10, 'margin_right' => 10]
        );
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

        $validated['payment_method'] = PaymentMethod::whereKey($validated['payment_method_id'])->value('code');

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

    /**
     * Check whether a member already has a deposit recorded for a given month.
     * Used by the deposit forms to warn before submitting.
     */
    public function checkMonth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        $exists = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        return response()->json([
            'exists' => $exists,
            'month_label' => $monthDate->format('M Y'),
        ]);
    }

    /**
     * One-click mark-as-paid: records a deposit for a member + month with
     * auto-filled amount (from member's shares), a generated transaction id,
     * and the default Bank Transfer payment method. No manual fields.
     */
    public function markPaid(Request $request): JsonResponse
    {
        $this->authorize('create', SavingsEntry::class);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $member = \App\Models\Member::findOrFail($validated['member_id']);
        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        // Enforce one deposit per member per month.
        $alreadyDeposited = \App\Models\MemberDepositMonth::where('member_id', $member->id)
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        if ($alreadyDeposited) {
            return response()->json([
                'message' => "{$member->name} already has a deposit for " . $monthDate->format('M Y') . '.',
            ], 422);
        }

        $amount = $member->getCalculatedMonthlyDepositAmount();

        if ($amount <= 0) {
            return response()->json([
                'message' => "Cannot record a deposit for {$member->name}: monthly amount is 0. Assign shares first.",
            ], 422);
        }

        // Resolve default payment method (Bank Transfer), fall back to any active one.
        $paymentMethod = PaymentMethod::where('code', 'bank_transfer')->first()
            ?? PaymentMethod::active()->ordered()->first();

        if (! $paymentMethod) {
            return response()->json([
                'message' => 'No payment method configured.',
            ], 422);
        }

        // Generate a unique transaction id.
        do {
            $transactionId = 'AUTO-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
        } while (SavingsEntry::where('transaction_id', $transactionId)->exists());

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($member, $monthDate, $amount, $paymentMethod, $transactionId) {
            $savingsEntry = SavingsEntry::create([
                'member_id' => $member->id,
                'amount' => $amount,
                'deposit_date' => $monthDate->copy()->endOfMonth(),
                'payment_method_id' => $paymentMethod->id,
                'payment_method' => $paymentMethod->code,
                'transaction_id' => $transactionId,
                'notes' => 'Marked as paid for ' . $monthDate->format('F Y'),
                'recorded_by' => auth()->id(),
            ]);

            \App\Models\MemberDepositMonth::create([
                'member_id' => $member->id,
                'month' => $monthDate->month,
                'year' => $monthDate->year,
                'savings_entry_id' => $savingsEntry->id,
            ]);

            return $savingsEntry;
        });

        return response()->json([
            'message' => "Deposit recorded for {$member->name} ({$monthDate->format('M Y')}).",
            'amount' => $amount,
            'transaction_id' => $transactionId,
        ], 201);
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

        // Enforce one deposit per member per month.
        $alreadyDeposited = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        if ($alreadyDeposited) {
            return response()->json([
                'message' => 'This member already has a deposit for ' . $monthDate->format('M Y') . '. Only one deposit per month is allowed.',
                'errors' => [
                    'month' => ['A deposit for this month already exists for this member.'],
                ],
            ], 422);
        }

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $monthDate) {
            $savingsEntry = SavingsEntry::create([
                'member_id' => $validated['member_id'],
                'amount' => $validated['amount'],
                'deposit_date' => $monthDate->endOfMonth(),
                'payment_method_id' => $validated['payment_method_id'],
                'payment_method' => PaymentMethod::whereKey($validated['payment_method_id'])->value('code'),
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            // Record the month as paid
            \App\Models\MemberDepositMonth::create([
                'member_id' => $validated['member_id'],
                'month' => $monthDate->month,
                'year' => $monthDate->year,
                'savings_entry_id' => $savingsEntry->id,
            ]);

            return $savingsEntry;
        });

        return response()->json([
            'message' => 'Deposit recorded successfully',
            'deposit' => $savingsEntry,
        ], 201);
    }
}
