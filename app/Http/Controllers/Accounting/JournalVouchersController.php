<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\ChartOfAccount;
use App\Services\Accounting\JournalEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalVouchersController extends Controller
{
    private JournalEngine $journalEngine;

    public function __construct(JournalEngine $journalEngine)
    {
        $this->journalEngine = $journalEngine;
    }

    public function index(): JsonResponse
    {
        $this->authorize('view', JournalVoucher::class);

        $query = JournalVoucher::with('entries.account', 'createdBy', 'postedBy')
            ->ordered();

        // Filter by status
        if (request()->has('status')) {
            $query->byStatus(request()->query('status'));
        }

        // Filter by date range
        if (request()->has('from_date') && request()->has('to_date')) {
            $query->byDateRange(request()->query('from_date'), request()->query('to_date'));
        }

        // Filter by source module
        if (request()->has('source_module')) {
            $query->bySourceModule(request()->query('source_module'));
        }

        $vouchers = $query->paginate(50);

        return response()->json($vouchers);
    }

    public function show(JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('view', $journalVoucher);

        return response()->json($journalVoucher->load('entries.account', 'createdBy', 'postedBy', 'reversedBy'));
    }

    public function create(): JsonResponse
    {
        $this->authorize('create', JournalVoucher::class);

        $accounts = ChartOfAccount::active()->ordered()->get();
        $voucherTypes = ['MANUAL', 'DEPOSIT', 'EXPENSE', 'INVESTMENT', 'SHARE'];

        return response()->json([
            'accounts' => $accounts,
            'voucher_types' => $voucherTypes,
            'voucher_number' => JournalVoucher::generateVoucherNumber(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', JournalVoucher::class);

        $validated = $request->validate([
            'voucher_number' => ['nullable', 'string', 'max:100', 'unique:journal_vouchers,voucher_number'],
            'voucher_date' => ['required', 'date'],
            'voucher_type' => ['required', 'in:MANUAL,DEPOSIT,EXPENSE,INVESTMENT,SHARE,REVERSAL'],
            'source_module' => ['nullable', 'string', 'max:100'],
            'source_record_id' => ['nullable', 'integer'],
            'description' => ['required', 'string'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.account_id' => ['required', 'exists:chart_of_accounts,id'],
            'entries.*.debit_amount' => ['nullable', 'numeric', 'min:0'],
            'entries.*.credit_amount' => ['nullable', 'numeric', 'min:0'],
            'entries.*.description' => ['nullable', 'string'],
        ]);

        // Create voucher
        $voucher = $this->journalEngine->createVoucher($validated, auth()->user());

        // Add entries
        foreach ($validated['entries'] as $entryData) {
            $account = ChartOfAccount::find($entryData['account_id']);
            $this->journalEngine->addEntry(
                $voucher,
                $account,
                $entryData['debit_amount'] ?? null,
                $entryData['credit_amount'] ?? null,
                $entryData['description'] ?? null
            );
        }

        // Validate the voucher
        $errors = $this->journalEngine->validateVoucher($voucher);
        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors,
            ], 422);
        }

        return response()->json($voucher->load('entries.account'), 201);
    }

    public function edit(JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('update', $journalVoucher);

        if (!$this->journalEngine->canEditVoucher($journalVoucher)) {
            return response()->json([
                'error' => 'Only draft vouchers can be edited',
            ], 403);
        }

        $accounts = ChartOfAccount::active()->ordered()->get();

        return response()->json([
            'voucher' => $journalVoucher->load('entries'),
            'accounts' => $accounts,
        ]);
    }

    public function update(Request $request, JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('update', $journalVoucher);

        if (!$this->journalEngine->canEditVoucher($journalVoucher)) {
            return response()->json([
                'error' => 'Only draft vouchers can be edited',
            ], 403);
        }

        $validated = $request->validate([
            'voucher_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.account_id' => ['required', 'exists:chart_of_accounts,id'],
            'entries.*.debit_amount' => ['nullable', 'numeric', 'min:0'],
            'entries.*.credit_amount' => ['nullable', 'numeric', 'min:0'],
            'entries.*.description' => ['nullable', 'string'],
        ]);

        // Update voucher
        $journalVoucher->update([
            'voucher_date' => $validated['voucher_date'],
            'description' => $validated['description'],
        ]);

        // Clear and re-add entries
        $this->journalEngine->removeAllEntries($journalVoucher);

        foreach ($validated['entries'] as $entryData) {
            $account = ChartOfAccount::find($entryData['account_id']);
            $this->journalEngine->addEntry(
                $journalVoucher,
                $account,
                $entryData['debit_amount'] ?? null,
                $entryData['credit_amount'] ?? null,
                $entryData['description'] ?? null
            );
        }

        // Validate the voucher
        $errors = $this->journalEngine->validateVoucher($journalVoucher);
        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors,
            ], 422);
        }

        return response()->json($journalVoucher->load('entries.account'));
    }

    public function destroy(JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('delete', $journalVoucher);

        if (!$this->journalEngine->canDeleteVoucher($journalVoucher)) {
            return response()->json([
                'error' => 'Only draft vouchers can be deleted',
            ], 403);
        }

        $journalVoucher->delete();

        return response()->json(null, 204);
    }

    public function post(Request $request, JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('update', $journalVoucher);

        if (!$this->journalEngine->canPostVoucher($journalVoucher)) {
            $errors = $this->journalEngine->validateVoucher($journalVoucher);

            return response()->json([
                'error' => 'Voucher cannot be posted',
                'errors' => $errors,
            ], 422);
        }

        $this->journalEngine->postVoucher($journalVoucher, auth()->user());

        return response()->json($journalVoucher->fresh()->load('entries.account', 'postedBy'));
    }

    public function reverse(Request $request, JournalVoucher $journalVoucher): JsonResponse
    {
        $this->authorize('delete', $journalVoucher);

        $validated = $request->validate([
            'reversal_reason' => ['required', 'string', 'max:500'],
        ]);

        if (!$this->journalEngine->canReverseVoucher($journalVoucher)) {
            return response()->json([
                'error' => 'Only posted vouchers can be reversed',
            ], 403);
        }

        $reversalVoucher = $this->journalEngine->reverseVoucher(
            $journalVoucher,
            auth()->user(),
            $validated['reversal_reason']
        );

        return response()->json([
            'original_voucher' => $journalVoucher->fresh(),
            'reversal_voucher' => $reversalVoucher->load('entries.account'),
        ]);
    }

    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.debit_amount' => ['nullable', 'numeric', 'min:0'],
            'entries.*.credit_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($validated['entries'] as $entry) {
            $totalDebits += $entry['debit_amount'] ?? 0;
            $totalCredits += $entry['credit_amount'] ?? 0;
        }

        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;

        return response()->json([
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => abs($totalDebits - $totalCredits),
            'is_balanced' => $isBalanced,
        ]);
    }
}
