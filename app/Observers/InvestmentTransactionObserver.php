<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\InvestmentTransaction;

class InvestmentTransactionObserver
{
    public function created(InvestmentTransaction $transaction): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_transaction_created',
            'entity_type' => 'InvestmentTransaction',
            'entity_id' => $transaction->id,
            'new_value' => $transaction->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function updated(InvestmentTransaction $transaction): void
    {
        $original = $transaction->getOriginal();
        $changes = $transaction->getChanges();

        if (empty($changes)) {
            return;
        }

        $oldValue = array_intersect_key($original, array_flip(array_keys($changes)));

        $actionType = $transaction->status === 'processed' ? 'investment_transaction_approved' : 'updated';

        if ($transaction->status === 'reversed') {
            $actionType = 'investment_transaction_reversed';
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'entity_type' => 'InvestmentTransaction',
            'entity_id' => $transaction->id,
            'old_value' => $oldValue,
            'new_value' => $changes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }
}
