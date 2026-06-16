<?php

namespace App\Policies;

use App\Models\JournalVoucher;
use App\Models\User;

class JournalVoucherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view accounting');
    }

    public function view(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('view accounting');
    }

    public function create(User $user): bool
    {
        return $user->can('create accounting entries');
    }

    public function update(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('update accounting entries') && $voucher->isDraft();
    }

    public function delete(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('delete accounting entries') && $voucher->isDraft();
    }

    public function post(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('post accounting entries') && $voucher->isDraft();
    }

    public function reverse(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('reverse accounting entries') && $voucher->isPosted();
    }
}
