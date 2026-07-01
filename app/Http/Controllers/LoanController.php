<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Support\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Loan::class);

        $loans = Loan::with(['member', 'repayments'])->latest('taken_date')->latest('id')->get();

        $totalLent = $loans->whereIn('status', ['active', 'repaid'])->sum('loan_amount');
        $totalRepaid = $loans->sum('total_repaid');
        $outstanding = $loans->where('status', 'active')->sum('outstanding_balance');
        $pendingCount = $loans->where('status', 'pending')->count();
        $overdueCount = $loans->filter(fn (Loan $l) => $l->is_overdue)->count();

        return view('loans.index', [
            'loans' => $loans,
            'totalLent' => $totalLent,
            'totalRepaid' => $totalRepaid,
            'outstanding' => $outstanding,
            'pendingCount' => $pendingCount,
            'overdueCount' => $overdueCount,
            'members' => Member::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Loan::class);

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $status = $request->get('status', '');
        $memberId = $request->get('member_id', '');
        $fromMonth = $request->get('from_month', '');
        $toMonth = $request->get('to_month', '');

        $query = Loan::with(['member', 'repayments']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%"));
            });
        }

        if ($status) {
            $query->where('status', $status);
        }
        if ($memberId) {
            $query->where('member_id', $memberId);
        }
        if (preg_match('/^\d{4}-\d{2}$/', $fromMonth)) {
            $query->where('taken_date', '>=', \Carbon\Carbon::createFromFormat('Y-m', $fromMonth)->startOfMonth());
        }
        if (preg_match('/^\d{4}-\d{2}$/', $toMonth)) {
            $query->where('taken_date', '<=', \Carbon\Carbon::createFromFormat('Y-m', $toMonth)->endOfMonth());
        }

        $filtered = $query->count();
        $total = Loan::count();

        $loans = $query->latest('taken_date')->latest('id')
            ->offset($start)->limit($length)->get()
            ->map(fn (Loan $l) => [
                'id' => $l->id,
                'loan_code' => $l->loan_code,
                'member_id' => $l->member_id,
                'member_name' => $l->member?->name ?? 'N/A',
                'member_status' => $l->member?->status,
                'loan_amount' => number_format($l->loan_amount, 2),
                'total_payable' => number_format($l->total_payable, 2),
                'outstanding' => number_format($l->outstanding_balance, 2),
                'taken_date' => $l->taken_date?->format('d M Y'),
                'due_date' => $l->due_date?->format('d M Y') ?? '-',
                'status' => $l->display_status,
            ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $loans,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Loan::class);

        return view('loans.create', [
            'members' => Member::where('status', 'active')->orderBy('name')->get(),
            'nextLoanCode' => $this->generateLoanCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Loan::class);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'service_charge' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'taken_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:taken_date'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        // Block loans for deactivated members.
        if (Member::whereKey($validated['member_id'])->value('status') !== 'active') {
            return back()->withInput()->withErrors([
                'member_id' => 'This member is deactivated — no new loans can be issued.',
            ]);
        }

        $validated['service_charge'] = $validated['service_charge'] ?? 0;
        $validated['status'] = 'pending';
        $validated['recorded_by'] = $request->user()->id;
        $validated['loan_code'] = $this->generateLoanCode();

        $loan = Loan::create($validated);
        $loan->logStatus('pending', $request->user()->id, 'Loan request created');

        Notify::admins(
            'Loan awaiting approval',
            ($loan->member?->name ?? 'A member') . ' — Tk ' . number_format($loan->loan_amount, 0),
            'credit-card',
            route('loans.show', $loan),
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan request created and submitted for approval.');
    }

    public function show(Loan $loan): View
    {
        $this->authorize('view', $loan);

        $loan->load(['member', 'recorder', 'approver', 'repayments.recorder', 'repayments.paymentMethod', 'statusHistories.changedBy']);

        return view('loans.show', [
            'loan' => $loan,
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function edit(Loan $loan): View|RedirectResponse
    {
        $this->authorize('update', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be edited.');
        }

        return view('loans.edit', [
            'loan' => $loan,
            'members' => Member::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('update', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be edited.');
        }

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'service_charge' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'taken_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:taken_date'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['service_charge'] = $validated['service_charge'] ?? 0;
        $loan->update($validated);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->authorize('delete', $loan);

        if (! in_array($loan->status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Only pending or rejected loans can be deleted.');
        }

        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted.');
    }

    // ---------------------------------------------------------- Approval flow

    public function approve(Loan $loan): View|RedirectResponse
    {
        $this->authorize('approve', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $loan->load(['member', 'recorder']);

        return view('loans.approve', ['loan' => $loan]);
    }

    public function approveStore(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending loans can be approved.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // logStatus reads the original status, so record the transition before updating.
        $loan->logStatus('active', $request->user()->id, $validated['notes'] ?? 'Loan approved & disbursed');
        $loan->update([
            'status' => 'active',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        // Observer posts the disbursement voucher (Dr 1500 / Cr 1200) on this transition.

        Notify::admins(
            'Loan approved & disbursed',
            ($loan->member?->name ?? 'A member') . ' — Tk ' . number_format($loan->loan_amount, 0),
            'credit-card',
            route('loans.show', $loan),
        );

        return redirect()->route('loans.show', $loan)
            ->with('toast', ['type' => 'success', 'message' => 'Loan approved and disbursed.']);
    }

    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending loans can be rejected.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $loan->logStatus('rejected', $request->user()->id, $validated['notes'] ?? 'Loan rejected');
        $loan->update(['status' => 'rejected']);

        return redirect()->route('loans.show', $loan)
            ->with('toast', ['type' => 'warning', 'message' => 'Loan request rejected.']);
    }

    public function writeOff(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('manage', Loan::class);

        if ($loan->status !== 'active') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only active loans can be written off.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $loan->logStatus('written_off', $request->user()->id, $validated['notes'] ?? 'Loan written off');
        $loan->update(['status' => 'written_off']);

        return redirect()->route('loans.show', $loan)
            ->with('toast', ['type' => 'warning', 'message' => 'Loan written off.']);
    }

    // ------------------------------------------------------------- Repayments

    public function recordRepayment(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('update', $loan);

        if ($loan->status !== 'active') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Repayments can only be recorded on active loans.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'repaid_date' => ['required', 'date'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $outstanding = $loan->outstanding_balance;
        if ($validated['amount'] > $outstanding + 0.001) {
            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Amount exceeds the outstanding balance of Tk ' . number_format($outstanding, 2) . '.',
            ]);
        }

        DB::transaction(function () use ($loan, $validated, $request) {
            $loan->repayments()->create([
                'amount' => $validated['amount'],
                'repaid_date' => $validated['repaid_date'],
                'payment_method_id' => $validated['payment_method_id'] ?? null,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);
            // Observer posts the repayment voucher (Dr 1200 / Cr 1500).

            $loan->syncRepaymentStatus($request->user()->id);
        });

        $loan->refresh();
        $message = $loan->status === 'repaid'
            ? 'Repayment recorded — loan fully repaid.'
            : 'Repayment recorded. Outstanding: Tk ' . number_format($loan->outstanding_balance, 2) . '.';

        return redirect()->route('loans.show', $loan)
            ->with('toast', ['type' => 'success', 'message' => $message]);
    }

    public function deleteRepayment(LoanRepayment $repayment): RedirectResponse
    {
        $loan = $repayment->loan;
        $this->authorize('update', $loan);

        DB::transaction(function () use ($repayment, $loan) {
            $repayment->delete();
            $loan->syncRepaymentStatus();
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Repayment removed.']);
    }

    /** Sequential loan code L0001, L0002, … (continues from the highest existing). */
    private function generateLoanCode(): string
    {
        $max = (int) Loan::withTrashed()
            ->where('loan_code', 'REGEXP', '^L[0-9]+$')
            ->selectRaw('MAX(CAST(SUBSTRING(loan_code, 2) AS UNSIGNED)) as mx')
            ->value('mx');

        return 'L' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}
