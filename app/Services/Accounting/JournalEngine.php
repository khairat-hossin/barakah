<?php

namespace App\Services\Accounting;

use App\Models\AccountingAuditLog;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JournalEngine
{
    public function createVoucher(array $data, User $user): JournalVoucher
    {
        return DB::transaction(function () use ($data, $user) {
            $data['created_by'] = $user->id;
            $data['status'] = 'DRAFT';
            $data['voucher_number'] = $data['voucher_number'] ?? JournalVoucher::generateVoucherNumber();

            $voucher = JournalVoucher::create($data);

            AccountingAuditLog::log(
                'journal_voucher',
                $voucher->id,
                'CREATED',
                $user,
                null,
                $voucher->only(['voucher_number', 'voucher_date', 'description', 'status'])
            );

            return $voucher;
        });
    }

    public function addEntry(
        JournalVoucher $voucher,
        ChartOfAccount $account,
        ?float $debitAmount = null,
        ?float $creditAmount = null,
        string $description = null,
        int $sequence = null
    ): JournalEntry {
        if ($sequence === null) {
            $sequence = $voucher->entries()->count() + 1;
        }

        return JournalEntry::create([
            'voucher_id' => $voucher->id,
            'account_id' => $account->id,
            'debit_amount' => $debitAmount,
            'credit_amount' => $creditAmount,
            'description' => $description,
            'entry_sequence' => $sequence,
        ]);
    }

    public function addMultipleEntries(JournalVoucher $voucher, array $entries): void
    {
        $sequence = 1;

        foreach ($entries as $entry) {
            $this->addEntry(
                $voucher,
                ChartOfAccount::find($entry['account_id']),
                $entry['debit_amount'] ?? null,
                $entry['credit_amount'] ?? null,
                $entry['description'] ?? null,
                $sequence++
            );
        }
    }

    public function updateEntry(
        JournalEntry $entry,
        ?float $debitAmount = null,
        ?float $creditAmount = null,
        string $description = null
    ): JournalEntry {
        $entry->update([
            'debit_amount' => $debitAmount,
            'credit_amount' => $creditAmount,
            'description' => $description,
        ]);

        return $entry;
    }

    public function deleteEntry(JournalEntry $entry): bool
    {
        if ($entry->voucher->isPosted()) {
            return false;
        }

        return $entry->delete();
    }

    public function removeAllEntries(JournalVoucher $voucher): bool
    {
        if ($voucher->isPosted()) {
            return false;
        }

        $voucher->entries()->delete();

        return true;
    }

    public function postVoucher(JournalVoucher $voucher, User $user): bool
    {
        return DB::transaction(function () use ($voucher, $user) {
            if (!$voucher->isDraft()) {
                return false;
            }

            if (empty($voucher->entries) && $voucher->entries()->count() === 0) {
                return false;
            }

            if (!$voucher->isBalanced()) {
                return false;
            }

            return $voucher->post($user);
        });
    }

    public function reverseVoucher(JournalVoucher $voucher, User $user, string $reason, array $newEntries = null): ?JournalVoucher
    {
        return DB::transaction(function () use ($voucher, $user, $reason, $newEntries) {
            if (!$voucher->isPosted()) {
                return null;
            }

            $voucher->reverse($user, $reason);

            // Create reversal voucher with opposite entries
            $reversalVoucher = $this->createVoucher([
                'voucher_number' => 'RV-' . JournalVoucher::generateVoucherNumber(),
                'voucher_date' => now()->toDateString(),
                'voucher_type' => 'REVERSAL',
                'source_module' => $voucher->source_module,
                'source_record_id' => $voucher->source_record_id,
                'description' => 'Reversal of ' . $voucher->voucher_number . ': ' . $reason,
                'status' => 'POSTED',
                'posted_date' => now(),
                'posted_by' => $user->id,
            ], $user);

            // Create opposite entries
            foreach ($voucher->entries as $entry) {
                $this->addEntry(
                    $reversalVoucher,
                    $entry->account,
                    $entry->credit_amount,
                    $entry->debit_amount,
                    'Reversal: ' . $entry->description,
                    $entry->entry_sequence
                );
            }

            // If new entries are provided, add them as well
            if ($newEntries) {
                $this->addMultipleEntries($reversalVoucher, $newEntries);
            }

            return $reversalVoucher;
        });
    }

    public function getVoucherBalance(JournalVoucher $voucher): array
    {
        $debits = 0;
        $credits = 0;

        foreach ($voucher->entries as $entry) {
            $debits += $entry->debit_amount ?? 0;
            $credits += $entry->credit_amount ?? 0;
        }

        return [
            'debits' => $debits,
            'credits' => $credits,
            'difference' => abs($debits - $credits),
            'is_balanced' => abs($debits - $credits) < 0.01,
        ];
    }

    public function validateVoucher(JournalVoucher $voucher): array
    {
        $errors = [];

        if ($voucher->entries()->count() === 0) {
            $errors[] = 'Voucher must have at least one entry';
        }

        if (!$voucher->isBalanced()) {
            $balance = $this->getVoucherBalance($voucher);
            $errors[] = 'Voucher is not balanced. Debits: ' . $balance['debits'] . ', Credits: ' . $balance['credits'];
        }

        if (empty($voucher->description)) {
            $errors[] = 'Voucher description is required';
        }

        return $errors;
    }

    public function canEditVoucher(JournalVoucher $voucher): bool
    {
        return $voucher->isDraft();
    }

    public function canDeleteVoucher(JournalVoucher $voucher): bool
    {
        return $voucher->isDraft();
    }

    public function canPostVoucher(JournalVoucher $voucher): bool
    {
        return $voucher->isDraft() && empty($this->validateVoucher($voucher));
    }

    public function canReverseVoucher(JournalVoucher $voucher): bool
    {
        return $voucher->isPosted() && !$voucher->isReversed();
    }
}
