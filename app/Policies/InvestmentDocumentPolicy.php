<?php

namespace App\Policies;

use App\Models\InvestmentDocument;
use App\Models\User;

class InvestmentDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view investments');
    }

    public function view(User $user, InvestmentDocument $document): bool
    {
        // Only uploader or admin can view
        return $document->uploaded_by === $user->id || $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->can('manage investment documents');
    }

    public function update(User $user, InvestmentDocument $document): bool
    {
        return $user->can('manage investment documents') &&
               ($document->uploaded_by === $user->id || $user->hasRole('Super Admin'));
    }

    public function delete(User $user, InvestmentDocument $document): bool
    {
        return $user->can('delete investment documents') &&
               ($document->uploaded_by === $user->id || $user->hasRole('Super Admin'));
    }

    public function verify(User $user, InvestmentDocument $document): bool
    {
        return !$document->isVerified() &&
               $user->can('verify investment documents');
    }

    public function download(User $user, InvestmentDocument $document): bool
    {
        return $document->uploaded_by === $user->id ||
               $document->is_public ||
               $user->can('view investments');
    }
}
