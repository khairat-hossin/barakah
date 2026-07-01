<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view expenses');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->can('view expenses');
    }

    public function create(User $user): bool
    {
        return $user->can('create expenses');
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->can('update expenses');
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->can('delete expenses');
    }

    public function approve(User $user, Expense $expense): bool
    {
        return $user->can('approve expenses');
    }

    public function manage(User $user): bool
    {
        return $user->can('manage expenses');
    }
}
