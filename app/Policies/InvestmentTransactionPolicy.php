<?php

namespace App\Policies;

use App\Models\InvestmentTransaction;
use App\Models\User;

class InvestmentTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view investments');
    }

    public function view(User $user, InvestmentTransaction $transaction): bool
    {
        return $user->can('view investments');
    }

    public function create(User $user): bool
    {
        return $user->can('create investment transactions');
    }

    public function approve(User $user, InvestmentTransaction $transaction): bool
    {
        return $transaction->status === 'pending' &&
               $user->can('approve investment transactions');
    }

    public function reverse(User $user, InvestmentTransaction $transaction): bool
    {
        return in_array($transaction->status, ['processed', 'pending']) &&
               $user->can('manage investment transactions');
    }
}
