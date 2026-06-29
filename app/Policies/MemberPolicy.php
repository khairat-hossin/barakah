<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view members');
    }

    public function view(User $user, Member $member): bool
    {
        return $user->can('view members');
    }

    public function create(User $user): bool
    {
        return $user->can('create members');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->can('update members');
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->can('delete members');
    }
}
