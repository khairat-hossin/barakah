<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Investment;

class InvestmentObserver
{
    public function created(Investment $investment): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_created',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'new_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function updated(Investment $investment): void
    {
        // Get original values
        $original = $investment->getOriginal();
        $changes = $investment->getChanges();

        // Only log if something actually changed
        if (empty($changes)) {
            return;
        }

        $oldValue = array_intersect_key($original, array_flip(array_keys($changes)));

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_updated',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'old_value' => $oldValue,
            'new_value' => $changes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function deleted(Investment $investment): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'deleted',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'old_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function restored(Investment $investment): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'restored',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'new_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }
}
