<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\ChartOfAccountsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartOfAccountsController extends Controller
{
    private ChartOfAccountsService $coaService;

    public function __construct(ChartOfAccountsService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index(): \Illuminate\View\View
    {
        $this->authorize('view', ChartOfAccount::class);

        $type = request('type');
        $query = ChartOfAccount::active()->with('parent', 'children')->ordered();

        if ($type) {
            $query->where('account_type', $type);
        }

        $accounts = $query->paginate(50);

        $totalAssets = $this->coaService->calculateTotalAssets();
        $totalLiabilities = $this->coaService->calculateTotalLiabilities();
        $totalEquity = $this->coaService->calculateTotalEquity();

        return view('accounting.chart-of-accounts.index', compact('accounts', 'totalAssets', 'totalLiabilities', 'totalEquity'));
    }

    public function tree(): JsonResponse
    {
        $this->authorize('view', ChartOfAccount::class);

        $hierarchy = $this->coaService->getAccountHierarchy();

        return response()->json($hierarchy);
    }

    public function show(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('view', $chartOfAccount);

        return response()->json($chartOfAccount->load('parent', 'children', 'journalEntries'));
    }

    public function create(): JsonResponse
    {
        $this->authorize('create', ChartOfAccount::class);

        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $normalBalances = ['DEBIT', 'CREDIT'];
        $parentAccounts = ChartOfAccount::active()
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return response()->json([
            'account_types' => $accountTypes,
            'normal_balances' => $normalBalances,
            'parent_accounts' => $parentAccounts,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', ChartOfAccount::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:chart_of_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'account_type' => ['required', 'in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE'],
            'normal_balance' => ['required', 'in:DEBIT,CREDIT'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $account = $this->coaService->createAccount($validated, auth()->user());

        return response()->json($account, 201);
    }

    public function edit(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('update', $chartOfAccount);

        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $normalBalances = ['DEBIT', 'CREDIT'];
        $parentAccounts = ChartOfAccount::active()
            ->whereNull('parent_id')
            ->where('id', '!=', $chartOfAccount->id)
            ->ordered()
            ->get();

        return response()->json([
            'account' => $chartOfAccount,
            'account_types' => $accountTypes,
            'normal_balances' => $normalBalances,
            'parent_accounts' => $parentAccounts,
        ]);
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('update', $chartOfAccount);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:chart_of_accounts,code,' . $chartOfAccount->id],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id', 'not_in:' . $chartOfAccount->id],
            'account_type' => ['required', 'in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE'],
            'normal_balance' => ['required', 'in:DEBIT,CREDIT'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $account = $this->coaService->updateAccount($chartOfAccount, $validated, auth()->user());

        return response()->json($account);
    }

    public function destroy(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('delete', $chartOfAccount);

        // Check if account has journal entries
        if ($chartOfAccount->journalEntries()->exists()) {
            return response()->json([
                'error' => 'Cannot delete account with existing journal entries',
            ], 422);
        }

        // Check if account has child accounts
        if ($chartOfAccount->children()->exists()) {
            return response()->json([
                'error' => 'Cannot delete account with child accounts',
            ], 422);
        }

        $chartOfAccount->delete();

        return response()->json(null, 204);
    }

    public function getBalance(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('view', $chartOfAccount);

        $fromDate = request()->query('from_date');
        $toDate = request()->query('to_date');

        $balance = $this->coaService->calculateAccountBalance($chartOfAccount, $fromDate, $toDate);

        return response()->json([
            'account' => [
                'id' => $chartOfAccount->id,
                'code' => $chartOfAccount->code,
                'name' => $chartOfAccount->name,
            ],
            'balance' => $balance,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);
    }

    public function activate(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('update', $chartOfAccount);

        $account = $this->coaService->activateAccount($chartOfAccount, auth()->user());

        return response()->json($account);
    }

    public function deactivate(ChartOfAccount $chartOfAccount): JsonResponse
    {
        $this->authorize('update', $chartOfAccount);

        $account = $this->coaService->deactivateAccount($chartOfAccount, auth()->user());

        return response()->json($account);
    }

    public function byType(string $type): JsonResponse
    {
        $this->authorize('view', ChartOfAccount::class);

        $validTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];

        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid account type'], 422);
        }

        $accounts = $this->coaService->getAccountsByType($type);

        return response()->json($accounts);
    }
}
