<?php

namespace App\Policies;

use App\Models\SavingsEntry;
use App\Models\User;

class SavingsEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view deposits');
    }

    public function view(User $user, SavingsEntry $savingsEntry): bool
    {
        return $user->can('view deposits');
    }

    public function create(User $user): bool
    {
        return $user->can('create deposits');
    }

    public function update(User $user, SavingsEntry $savingsEntry): bool
    {
        return $user->can('create deposits');
    }

    public function delete(User $user, SavingsEntry $savingsEntry): bool
    {
        return $user->can('create deposits');
    }
}
