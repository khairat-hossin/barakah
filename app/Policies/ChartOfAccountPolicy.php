<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;

class ChartOfAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view accounting');
    }

    public function view(User $user, ChartOfAccount $account): bool
    {
        return $user->can('view accounting');
    }

    public function create(User $user): bool
    {
        return $user->can('manage accounting');
    }

    public function update(User $user, ChartOfAccount $account): bool
    {
        return $user->can('manage accounting') && !$account->journalEntries()->exists();
    }

    public function delete(User $user, ChartOfAccount $account): bool
    {
        return $user->can('manage accounting')
            && !$account->journalEntries()->exists()
            && !$account->children()->exists();
    }
}
