<?php

namespace App\Policies;

use App\Models\Investment;
use App\Models\User;

class InvestmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view investments');
    }

    public function view(User $user, Investment $investment): bool
    {
        return $user->can('view investments');
    }

    public function create(User $user): bool
    {
        return $user->can('create investments');
    }

    public function update(User $user, Investment $investment): bool
    {
        // Only draft investments can be updated, and only by creator or admin
        if ($investment->status !== 'draft') {
            return false;
        }

        return $user->can('update investments') &&
               ($investment->created_by === $user->id || $user->hasRole('Super Admin'));
    }

    public function delete(User $user, Investment $investment): bool
    {
        // Only draft investments can be deleted, and only by creator or admin
        if ($investment->status !== 'draft') {
            return false;
        }

        return $user->can('delete investments') &&
               ($investment->created_by === $user->id || $user->hasRole('Super Admin'));
    }

    public function activate(User $user, Investment $investment): bool
    {
        return $investment->status === 'draft' &&
               $user->can('manage investments');
    }

    public function mature(User $user, Investment $investment): bool
    {
        return $investment->status === 'active' &&
               $user->can('manage investments');
    }

    public function suspend(User $user, Investment $investment): bool
    {
        return $investment->status === 'active' &&
               $user->can('manage investments');
    }

    public function close(User $user, Investment $investment): bool
    {
        return in_array($investment->status, ['active', 'suspended', 'matured']) &&
               $user->can('manage investments');
    }
}
