<?php

namespace App\Http\Controllers;

use App\Exports\SpreadsheetExporter;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\InvestmentType;
use App\Models\Loan;
use App\Models\PaymentMethod;
use App\Models\SavingsEntry;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Resolve the from/to date range from the request, defaulting to the
     * current month.
     */
    private function dateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? \Carbon\Carbon::parse($request->query('from'))->startOfDay()
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? \Carbon\Carbon::parse($request->query('to'))->endOfDay()
            : now()->endOfMonth();

        return [$from, $to];
    }

    /**
     * Resolve a from/to range from month inputs ("YYYY-MM"), covering whole
     * months and defaulting to the current month. Used by the deposit report.
     */
    private function monthRange(Request $request): array
    {
        $fromMonth = $request->query('from_month');
        $toMonth = $request->query('to_month');

        $from = preg_match('/^\d{4}-\d{2}$/', (string) $fromMonth)
            ? \Carbon\Carbon::createFromFormat('Y-m', $fromMonth)->startOfMonth()
            : now()->startOfMonth();
        $to = preg_match('/^\d{4}-\d{2}$/', (string) $toMonth)
            ? \Carbon\Carbon::createFromFormat('Y-m', $toMonth)->endOfMonth()
            : now()->endOfMonth();

        return [$from, $to];
    }

    // ---------------------------------------------------------------- Deposits

    private function depositsQuery(Request $request)
    {
        [$from, $to] = $this->monthRange($request);

        $query = SavingsEntry::with(['member', 'paymentMethod'])
            ->whereBetween('deposit_date', [$from, $to]);

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->query('payment_method_id'));
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->query('member_id'));
        }

        return $query->orderByDesc('deposit_date');
    }

    public function deposits(Request $request)
    {
        $this->authorize('viewAny', SavingsEntry::class);

        [$from, $to] = $this->monthRange($request);
        $entries = $this->depositsQuery($request)->get();

        return view('reports.deposits', [
            'entries' => $entries,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'fromMonth' => $from->format('Y-m'),
            'toMonth' => $to->format('Y-m'),
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
            'members' => \App\Models\Member::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['payment_method_id', 'member_id']),
            'totalAmount' => $entries->sum('amount'),
            'count' => $entries->count(),
            'average' => $entries->count() ? $entries->avg('amount') : 0,
        ]);
    }

    public function depositsPdf(Request $request)
    {
        $this->authorize('viewAny', SavingsEntry::class);
        [$from, $to] = $this->monthRange($request);
        $entries = $this->depositsQuery($request)->get();

        return $this->renderPdf('reports.pdf.deposits', [
            'entries' => $entries,
            'from' => $from,
            'to' => $to,
            'totalAmount' => $entries->sum('amount'),
            'count' => $entries->count(),
        ], 'deposit-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf');
    }

    public function depositsExcel(Request $request)
    {
        $this->authorize('viewAny', SavingsEntry::class);
        $entries = $this->depositsQuery($request)->get();

        $rows = $entries->map(fn ($e) => [
            $e->deposit_date?->format('Y-m-d'),
            $e->member?->name,
            $e->transaction_id,
            $e->paymentMethod?->name ?? $e->payment_method,
            (float) $e->amount,
        ]);

        return SpreadsheetExporter::download(
            ['Date', 'Member', 'Transaction ID', 'Payment Method', 'Amount'],
            $rows,
            'deposit-report-' . now()->format('Ymd') . '.xlsx',
            'Deposits'
        );
    }

    // ---------------------------------------------------------------- Expenses

    private function expensesQuery(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $query = Expense::with(['category', 'member'])
            ->whereBetween('expense_date', [$from, $to]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return $query->orderByDesc('expense_date');
    }

    public function expenses(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        [$from, $to] = $this->dateRange($request);
        $expenses = $this->expensesQuery($request)->get();

        return view('reports.expenses', [
            'expenses' => $expenses,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'statuses' => ['draft', 'pending', 'approved', 'paid'],
            'filters' => $request->only(['category_id', 'status']),
            'totalAmount' => $expenses->sum('amount'),
            'count' => $expenses->count(),
        ]);
    }

    public function expensesPdf(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        [$from, $to] = $this->dateRange($request);
        $expenses = $this->expensesQuery($request)->get();

        return $this->renderPdf('reports.pdf.expenses', [
            'expenses' => $expenses,
            'from' => $from,
            'to' => $to,
            'totalAmount' => $expenses->sum('amount'),
            'count' => $expenses->count(),
        ], 'expense-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf');
    }

    public function expensesExcel(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        $expenses = $this->expensesQuery($request)->get();

        $rows = $expenses->map(fn ($e) => [
            $e->expense_date?->format('Y-m-d'),
            $e->category?->name,
            $e->title,
            ucfirst($e->status),
            (float) $e->amount,
        ]);

        return SpreadsheetExporter::download(
            ['Date', 'Category', 'Title', 'Status', 'Amount'],
            $rows,
            'expense-report-' . now()->format('Ymd') . '.xlsx',
            'Expenses'
        );
    }

    // ------------------------------------------------------------- Investments

    private function investmentsQuery(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $query = Investment::with(['investmentType'])
            ->whereBetween('start_date', [$from, $to]);

        if ($request->filled('investment_type_id')) {
            $query->where('investment_type_id', $request->query('investment_type_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return $query->orderByDesc('start_date');
    }

    public function investments(Request $request)
    {
        $this->authorize('viewAny', Investment::class);

        [$from, $to] = $this->dateRange($request);
        $investments = $this->investmentsQuery($request)->get();

        return view('reports.investments', [
            'investments' => $investments,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'types' => InvestmentType::orderBy('name')->get(),
            'statuses' => ['draft', 'active', 'matured', 'closed', 'suspended'],
            'filters' => $request->only(['investment_type_id', 'status']),
            'totalInvested' => $investments->sum('total_invested_amount'),
            'totalReturned' => $investments->sum('total_returned_amount'),
            'netProfit' => $investments->sum('net_profit_loss'),
            'count' => $investments->count(),
        ]);
    }

    public function investmentsPdf(Request $request)
    {
        $this->authorize('viewAny', Investment::class);
        [$from, $to] = $this->dateRange($request);
        $investments = $this->investmentsQuery($request)->get();

        return $this->renderPdf('reports.pdf.investments', [
            'investments' => $investments,
            'from' => $from,
            'to' => $to,
            'totalInvested' => $investments->sum('total_invested_amount'),
            'totalReturned' => $investments->sum('total_returned_amount'),
            'netProfit' => $investments->sum('net_profit_loss'),
            'count' => $investments->count(),
        ], 'investment-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf');
    }

    public function investmentsExcel(Request $request)
    {
        $this->authorize('viewAny', Investment::class);
        $investments = $this->investmentsQuery($request)->get();

        $rows = $investments->map(fn ($i) => [
            $i->code,
            $i->name,
            $i->investmentType?->name,
            $i->start_date?->format('Y-m-d'),
            ucfirst($i->status),
            (float) $i->total_invested_amount,
            (float) $i->total_returned_amount,
            (float) $i->net_profit_loss,
        ]);

        return SpreadsheetExporter::download(
            ['Code', 'Name', 'Type', 'Start Date', 'Status', 'Invested', 'Returned', 'Net P/L'],
            $rows,
            'investment-report-' . now()->format('Ymd') . '.xlsx',
            'Investments'
        );
    }

    // ------------------------------------------------------------------- Loans

    private function loansQuery(Request $request)
    {
        [$from, $to] = $this->monthRange($request);

        $query = Loan::with(['member', 'repayments'])
            ->whereBetween('taken_date', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->query('member_id'));
        }

        return $query->orderByDesc('taken_date');
    }

    public function loans(Request $request)
    {
        $this->authorize('viewAny', Loan::class);

        [$from, $to] = $this->monthRange($request);
        $loans = $this->loansQuery($request)->get();

        return view('reports.loans', [
            'loans' => $loans,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'fromMonth' => $from->format('Y-m'),
            'toMonth' => $to->format('Y-m'),
            'statuses' => ['pending', 'active', 'repaid', 'rejected', 'written_off'],
            'members' => \App\Models\Member::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['status', 'member_id']),
            'totalLent' => $loans->sum('loan_amount'),
            'totalRepaid' => $loans->sum('total_repaid'),
            'totalOutstanding' => $loans->where('status', 'active')->sum('outstanding_balance'),
            'count' => $loans->count(),
        ]);
    }

    public function loansPdf(Request $request)
    {
        $this->authorize('viewAny', Loan::class);
        [$from, $to] = $this->monthRange($request);
        $loans = $this->loansQuery($request)->get();

        return $this->renderPdf('reports.pdf.loans', [
            'loans' => $loans,
            'from' => $from,
            'to' => $to,
            'totalLent' => $loans->sum('loan_amount'),
            'totalRepaid' => $loans->sum('total_repaid'),
            'totalOutstanding' => $loans->where('status', 'active')->sum('outstanding_balance'),
            'count' => $loans->count(),
        ], 'loan-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf');
    }

    public function loansExcel(Request $request)
    {
        $this->authorize('viewAny', Loan::class);
        $loans = $this->loansQuery($request)->get();

        $rows = $loans->map(fn (Loan $l) => [
            $l->loan_code,
            $l->member?->name,
            $l->taken_date?->format('Y-m-d'),
            $l->due_date?->format('Y-m-d'),
            ucfirst(str_replace('_', ' ', $l->status)),
            (float) $l->loan_amount,
            (float) $l->service_charge,
            (float) $l->total_repaid,
            (float) $l->outstanding_balance,
        ]);

        return SpreadsheetExporter::download(
            ['Code', 'Member', 'Taken', 'Due', 'Status', 'Loan Amount', 'Service Charge', 'Repaid', 'Outstanding'],
            $rows,
            'loan-report-' . now()->format('Ymd') . '.xlsx',
            'Loans'
        );
    }

    // ------------------------------------------------------------------- Shared

    /**
     * Render a Blade view to a downloadable PDF via mPDF.
     */
    private function renderPdf(string $view, array $data, string $filename)
    {
        // Smaller top margin so the header sits near the top of the page;
        // footer adds "system generated" note + page numbers on every page.
        return \App\Support\PdfRenderer::download($view, $data, $filename, [
            'margin_top' => 7,
            'footer' => true,
        ]);
    }
}
