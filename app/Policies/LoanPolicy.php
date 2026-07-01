<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view loans');
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->can('view loans');
    }

    public function create(User $user): bool
    {
        return $user->can('create loans');
    }

    public function update(User $user, Loan $loan): bool
    {
        return $user->can('update loans');
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $user->can('delete loans');
    }

    public function approve(User $user, Loan $loan): bool
    {
        return $user->can('approve loans');
    }

    public function manage(User $user): bool
    {
        return $user->can('manage loans');
    }
}
