<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentTransactionRequest;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Services\InvestmentTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvestmentTransactionController extends Controller
{
    public function __construct(
        private InvestmentTransactionService $transactionService,
    ) {
    }

    public function index(Investment $investment): View
    {
        $investment->load('transactions.creator', 'transactions.approver');
        $transactions = $investment->transactions()->latest('transaction_date')->paginate(20);

        return view('investments.transactions.index', [
            'investment' => $investment,
            'transactions' => $transactions,
        ]);
    }

    public function store(StoreInvestmentTransactionRequest $request, Investment $investment): RedirectResponse
    {
        $this->authorize('create', InvestmentTransaction::class);

        $transaction = $this->transactionService->recordTransaction(
            $investment,
            $request->validated()
        );

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Transaction recorded successfully.');
    }

    public function approve(Investment $investment, InvestmentTransaction $transaction): RedirectResponse
    {
        $this->authorize('approve', $transaction);

        $this->transactionService->approveTransaction($transaction);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Transaction approved successfully.');
    }

    public function reverse(Investment $investment, InvestmentTransaction $transaction): RedirectResponse
    {
        $this->authorize('reverse', $transaction);

        $reason = request('reason', 'Reversed by user');
        $this->transactionService->reverseTransaction($transaction, $reason);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Transaction reversed successfully.');
    }
}
