<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if projects table doesn't exist (fresh install)
        if (!Schema::hasTable('projects')) {
            return;
        }

        DB::transaction(function () {
            // Seed investment types first if not already seeded
            $investmentTypes = DB::table('investment_types')->get();

            if ($investmentTypes->isEmpty()) {
                // Seed the investment types
                $types = [
                    ['code' => 'BUSINESS_INVESTMENT', 'name' => 'Business Investment'],
                    ['code' => 'PARTNERSHIP', 'name' => 'Partnership'],
                    ['code' => 'TRADING', 'name' => 'Trading'],
                    ['code' => 'PROPERTY', 'name' => 'Property Investment'],
                    ['code' => 'FIXED_DEPOSIT', 'name' => 'Fixed Deposit (FDR)'],
                    ['code' => 'DPS', 'name' => 'Deposit Pension Scheme (DPS)'],
                    ['code' => 'SAVINGS_SCHEME', 'name' => 'Savings Scheme'],
                    ['code' => 'OTHER', 'name' => 'Other Investment'],
                ];

                foreach ($types as $type) {
                    DB::table('investment_types')->insert(array_merge($type, [
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }

                $investmentTypes = DB::table('investment_types')->get();
            }

            $investmentTypes = $investmentTypes->keyBy('code');

            $categoryMapping = [
                'business' => $investmentTypes->get('BUSINESS_INVESTMENT')?->id,
                'partnership' => $investmentTypes->get('PARTNERSHIP')?->id,
                'trading' => $investmentTypes->get('TRADING')?->id,
                'property' => $investmentTypes->get('PROPERTY')?->id,
                'fdr' => $investmentTypes->get('FIXED_DEPOSIT')?->id,
                'dps' => $investmentTypes->get('DPS')?->id,
                'savings' => $investmentTypes->get('SAVINGS_SCHEME')?->id,
            ];

            // Get a default investment type if category doesn't map
            $defaultType = $investmentTypes->get('OTHER')?->id;

            // Step 2: Migrate projects to investments
            $projects = DB::table('projects')->get();

            foreach ($projects as $project) {
                // Generate investment code (INV-YYYY-XXXXXX)
                $year = date('Y', strtotime($project->created_at));
                $sequenceNumber = DB::table('investments')
                    ->whereYear('created_at', $year)
                    ->count() + 1;
                $investmentCode = sprintf('INV-%d-%06d', $year, $sequenceNumber);

                // Map investment type
                $typeId = isset($categoryMapping[$project->category])
                    ? $categoryMapping[$project->category]
                    : $defaultType;

                // Projects don't have direct member relationship, set to null
                // This will be linked manually in the UI
                $investorId = null;

                // Map project status to investment status
                $statusMap = [
                    'draft' => 'draft',
                    'active' => 'active',
                    'completed' => 'matured',
                    'cancelled' => 'closed',
                ];
                $investmentStatus = $statusMap[$project->status] ?? 'draft';

                // Insert into investments table (let DB auto-generate ID)
                DB::table('investments')->insert([
                    'code' => $investmentCode,
                    'investment_type_id' => $typeId,
                    'investor_id' => $investorId,
                    'name' => $project->name,
                    'description' => null,
                    'status' => $investmentStatus,
                    'risk_level' => 'medium',
                    'return_type' => 'fixed',
                    'expected_return_percentage' => $project->expected_return_percentage ?? 0,
                    'actual_return_percentage' => null,
                    'tenure_months' => null,
                    'start_date' => $project->start_date ?? now()->toDateString(),
                    'maturity_date' => $project->deadline,
                    'closed_date' => null,
                    'total_invested_amount' => $project->budget_requested ?? 0,
                    'total_returned_amount' => 0,
                    'net_profit_loss' => 0,
                    'notes' => $project->notes,
                    'metadata' => json_encode([
                        'migrated_from_project' => true,
                        'original_project_id' => $project->id,
                        'original_category' => $project->category,
                        'original_progress' => $project->progress_percentage,
                    ]),
                    'created_by' => $project->user_id,
                    'updated_by' => null,
                    'deleted_at' => null,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ]);
            }

            // Step 5: Update expenses table to use investments
            // Note: This will be handled manually or through a separate migration
            // since the relationship between projects and expenses is complex

            // Step 6: Data Integrity Checks
            $projectCount = DB::table('projects')->count();
            $investmentCount = DB::selectOne("SELECT COUNT(*) as count FROM investments WHERE JSON_EXTRACT(metadata, '$.migrated_from_project') = true")->count;

            if ($projectCount !== $investmentCount) {
                throw new Exception("Data integrity check failed: Project count ($projectCount) != Investment count ($investmentCount)");
            }

            // Verify investor IDs are set
            $investmentsWithoutInvestor = DB::selectOne("SELECT COUNT(*) as count FROM investments WHERE JSON_EXTRACT(metadata, '$.migrated_from_project') = true AND investor_id IS NULL")->count;

            if ($investmentsWithoutInvestor > 0) {
                // This is acceptable - some projects might not have member mapping
                \Log::warning("$investmentsWithoutInvestor investments migrated without investor mapping");
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            // Delete all migrated investments and related data
            $migratedIds = DB::raw("SELECT id FROM investments WHERE JSON_EXTRACT(metadata, '$.migrated_from_project') = true");

            // Cascade delete will handle related tables
            DB::statement("DELETE FROM investments WHERE JSON_EXTRACT(metadata, '$.migrated_from_project') = true");

            // Revert expenses table
            // DB::table('expenses')->update(['project_id' => null]);
        });
    }
};
