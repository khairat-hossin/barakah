<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class AssignMemberCodes extends Command
{
    protected $signature = 'members:assign-codes {--force : Reassign codes to ALL members, overwriting existing ones}';

    protected $description = 'Assign sequential member codes (M0001, M0002, ...) to members, ordered by id.';

    public function handle(): int
    {
        // Without --force, only fill members missing a code; with --force, renumber everyone.
        $query = Member::orderBy('id');
        if (! $this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('member_code')->orWhere('member_code', '');
            });
        }

        $members = $query->get();

        if ($members->isEmpty()) {
            $this->info('No members to update.');
            return self::SUCCESS;
        }

        // Continue the sequence from the current highest M#### code.
        $start = (int) Member::where('member_code', 'REGEXP', '^M[0-9]+$')
            ->selectRaw('MAX(CAST(SUBSTRING(member_code, 2) AS UNSIGNED)) as mx')
            ->value('mx');

        // With --force we restart the whole sequence from 1.
        $n = $this->option('force') ? 0 : $start;

        $updated = 0;
        foreach ($members as $member) {
            $n++;
            $member->update(['member_code' => 'M' . str_pad($n, 4, '0', STR_PAD_LEFT)]);
            $updated++;
        }

        $this->info("Assigned codes to {$updated} member(s). Last code: M" . str_pad($n, 4, '0', STR_PAD_LEFT));

        return self::SUCCESS;
    }
}
