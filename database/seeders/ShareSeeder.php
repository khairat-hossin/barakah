<?php

namespace Database\Seeders;

use App\Models\Share;
use Illuminate\Database\Seeder;

class ShareSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();

        for ($i = 1; $i <= 40; $i++) {
            Share::firstOrCreate(
                ['share_number' => $i],
                [
                    'issue_date' => $today,
                    'status' => 'active',
                ]
            );
        }
    }
}
