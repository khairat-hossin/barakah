<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Share;
use App\Models\MemberShareOwnership;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportMembersFromExcel extends Command
{
    protected $signature = 'import:members {file : Path to the Excel file}';
    protected $description = 'Import members from an Excel file with name, phone, and share count';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        try {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $imported = 0;
            $skipped = 0;
            $errors = [];

            $rowNum = 2;
            while (true) {
                $name = trim($worksheet->getCell("A{$rowNum}")->getValue() ?? '');
                $phone = trim($worksheet->getCell("B{$rowNum}")->getValue() ?? '');
                $shareCount = (int)($worksheet->getCell("C{$rowNum}")->getValue() ?? 0);

                if (empty($name)) {
                    $rowNum++;
                    if ($rowNum > 1000) break;
                    continue;
                }
                $rowNum++;

                try {
                    // Check if member already exists
                    $member = Member::where('name', $name)->first();
                    if ($member) {
                        $this->warn("Member '{$name}' already exists, updating phone...");
                        if (!empty($phone)) {
                            $member->update(['phone' => $phone]);
                        }
                        $skipped++;
                    } else {
                        // Create new member
                        $member = Member::create([
                            'name' => $name,
                            'phone' => !empty($phone) ? $phone : null,
                            'status' => 'active',
                        ]);
                        $this->line("✓ Created member: {$name}");
                        $imported++;
                    }

                    // Assign shares
                    if ($shareCount > 0) {
                        $this->assignShares($member, $shareCount);
                        $this->line("  └─ Assigned {$shareCount} shares");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row with name '{$name}': " . $e->getMessage();
                    $this->error("  Error: " . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info("Import Summary:");
            $this->line("  • Created: {$imported}");
            $this->line("  • Updated: {$skipped}");

            if (!empty($errors)) {
                $this->newLine();
                $this->error("Errors encountered:");
                foreach ($errors as $error) {
                    $this->error("  • {$error}");
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error reading file: " . $e->getMessage());
            return 1;
        }
    }

    private function assignShares(Member $member, int $shareCount): void
    {
        // Get available unassigned shares
        $availableShares = Share::doesntHave('currentOwner')->limit($shareCount)->get();

        if ($availableShares->count() < $shareCount) {
            $this->warn("    Warning: Only {$availableShares->count()} shares available for {$shareCount} requested");
        }

        foreach ($availableShares as $share) {
            MemberShareOwnership::create([
                'member_id' => $member->id,
                'share_id' => $share->id,
                'ownership_start_date' => now(),
            ]);
        }
    }
}
