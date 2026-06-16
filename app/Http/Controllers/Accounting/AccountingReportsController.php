<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\FinancialStatementsService;
use App\Services\Accounting\GeneralLedgerService;
use App\Services\Accounting\TrialBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingReportsController extends Controller
{
    private GeneralLedgerService $ledgerService;
    private TrialBalanceService $trialBalanceService;
    private FinancialStatementsService $statementsService;

    public function __construct(
        GeneralLedgerService $ledgerService,
        TrialBalanceService $trialBalanceService,
        FinancialStatementsService $statementsService
    ) {
        $this->ledgerService = $ledgerService;
        $this->trialBalanceService = $trialBalanceService;
        $this->statementsService = $statementsService;
    }

    public function generalLedger(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $accounts = ChartOfAccount::active()->ordered()->get();

        $accountId = $request->query('account_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $ledgerData = null;
        $selectedAccount = null;

        if ($accountId) {
            $selectedAccount = ChartOfAccount::find($accountId);
            if ($selectedAccount) {
                $ledgerData = $this->ledgerService->getLedgerWithDetails(
                    $selectedAccount,
                    $fromDate,
                    $toDate
                );
            }
        }

        return view('accounting.reports.general-ledger', compact('accounts', 'ledgerData', 'selectedAccount'));
    }

    public function generalLedgerExport(Request $request)
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $validated = $request->validate([
            'account_id' => ['required', 'exists:chart_of_accounts,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        $account = ChartOfAccount::find($validated['account_id']);
        $csv = $this->ledgerService->exportLedgerToCsv(
            $account,
            $validated['from_date'] ?? null,
            $validated['to_date'] ?? null
        );

        return response()->streamDownload(
            fn() => print($csv),
            'general-ledger-' . $account->code . '.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function trialBalance(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $trialBalanceData = null;

        if ($fromDate && $toDate) {
            $trialBalanceData = $this->trialBalanceService->generateTrialBalance(
                null,
                $fromDate,
                $toDate
            );
        }

        return view('accounting.reports.trial-balance', compact('trialBalanceData'));
    }

    public function trialBalanceExport(Request $request)
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        $csv = $this->trialBalanceService->exportTrialBalanceToCsv(
            null,
            $validated['from_date'] ?? null,
            $validated['to_date'] ?? null
        );

        return response()->streamDownload(
            fn() => print($csv),
            'trial-balance.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function incomeStatement(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        $statement = $this->statementsService->getIncomeStatement($fromDate, $toDate);

        return view('accounting.reports.income-statement', compact('statement', 'fromDate', 'toDate'));
    }

    public function incomeStatementExport(Request $request)
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
        ]);

        $csv = $this->statementsService->exportIncomeStatementToCsv(
            $validated['from_date'],
            $validated['to_date']
        );

        return response()->streamDownload(
            fn() => print($csv),
            'income-statement.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function balanceSheet(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        $statement = $this->statementsService->getBalanceSheet($asOfDate);

        return view('accounting.reports.balance-sheet', compact('statement', 'asOfDate'));
    }

    public function balanceSheetExport(Request $request)
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $validated = $request->validate([
            'as_of_date' => ['nullable', 'date'],
        ]);

        $csv = $this->statementsService->exportBalanceSheetToCsv(
            $validated['as_of_date'] ?? null
        );

        return response()->streamDownload(
            fn() => print($csv),
            'balance-sheet.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function cashFlow(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        $statement = $this->statementsService->getCashFlowStatement($fromDate, $toDate);

        return view('accounting.reports.cash-flow', compact('statement', 'fromDate', 'toDate'));
    }

    public function fundPosition(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        $report = $this->statementsService->getFundPositionReport($asOfDate);

        return view('accounting.reports.fund-position', compact('report', 'asOfDate'));
    }

    public function accountAnalysis(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $validated = $request->validate([
            'account_id' => ['required', 'exists:chart_of_accounts,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        $account = ChartOfAccount::find($validated['account_id']);
        $analysis = $this->ledgerService->getAccountAnalysis(
            $account,
            $validated['from_date'] ?? null,
            $validated['to_date'] ?? null
        );

        return response()->json($analysis);
    }

    public function dashboard(): \Illuminate\View\View
    {
        $this->authorize('viewAny', ChartOfAccount::class);

        $asOfDate = now()->toDateString();
        $year = now()->year;
        $fromDate = $year . '-01-01';
        $toDate = now()->toDateString();

        $balanceSheet = $this->statementsService->getBalanceSheet($asOfDate);
        $incomeStatement = $this->statementsService->getIncomeStatement($fromDate, $toDate);
        $fundPosition = $this->statementsService->getFundPositionReport($asOfDate);
        $trialBalance = $this->trialBalanceService->generateTrialBalance(
            null,
            $fromDate,
            $toDate
        );

        $data = [
            'as_of_date' => $asOfDate,
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            'balance_sheet' => [
                'total_assets' => $balanceSheet['assets']['total'],
                'total_liabilities' => $balanceSheet['liabilities']['total'],
                'total_equity' => $balanceSheet['equity']['total'],
                'is_balanced' => $balanceSheet['is_balanced'],
            ],
            'income_statement' => [
                'total_income' => $incomeStatement['income']['total'],
                'total_expenses' => $incomeStatement['expenses']['total'],
                'net_profit' => $incomeStatement['net_profit'],
                'net_profit_percentage' => $incomeStatement['net_profit_percentage'],
            ],
            'fund_position' => [
                'operating_fund' => $fundPosition['operating_fund']['total'],
                'investment_fund' => $fundPosition['investment_fund']['amount'],
                'total_funds' => $fundPosition['total_funds'],
            ],
            'trial_balance' => [
                'total_debits' => $trialBalance['summary']['total_debits'],
                'total_credits' => $trialBalance['summary']['total_credits'],
                'is_balanced' => $trialBalance['summary']['is_balanced'],
            ],
        ];

        return view('accounting.reports.dashboard', compact('data'));
    }
}
