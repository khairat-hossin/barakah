<?php

namespace Database\Seeders;

use App\Models\InvestmentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvestmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'BUSINESS_INVESTMENT',
                'name' => 'Business Investment',
                'category' => 'Venture Capital',
                'description' => 'Investment in new or existing business ventures',
                'default_tenure_months' => 36,
                'default_return_type' => 'variable',
                'requires_approval' => true,
                'min_investment_amount' => 100000,
                'max_investment_amount' => 10000000,
                'features' => ['flexible_maturity', 'early_withdrawal'],
            ],
            [
                'code' => 'PARTNERSHIP',
                'name' => 'Partnership',
                'category' => 'Joint Ventures',
                'description' => 'Investment in business partnerships',
                'default_tenure_months' => 24,
                'default_return_type' => 'variable',
                'requires_approval' => true,
                'min_investment_amount' => 50000,
                'max_investment_amount' => 5000000,
                'features' => ['flexible_maturity', 'early_withdrawal'],
            ],
            [
                'code' => 'TRADING',
                'name' => 'Trading',
                'category' => 'Active Trading',
                'description' => 'Investment in trading activities (commodities, forex, etc.)',
                'default_tenure_months' => 12,
                'default_return_type' => 'variable',
                'requires_approval' => true,
                'min_investment_amount' => 25000,
                'max_investment_amount' => 2000000,
                'features' => ['flexible_maturity', 'early_withdrawal'],
            ],
            [
                'code' => 'PROPERTY',
                'name' => 'Property Investment',
                'category' => 'Real Estate',
                'description' => 'Investment in real estate and property',
                'default_tenure_months' => 60,
                'default_return_type' => 'fixed',
                'requires_approval' => true,
                'min_investment_amount' => 500000,
                'max_investment_amount' => 50000000,
                'features' => [],
            ],
            [
                'code' => 'FIXED_DEPOSIT',
                'name' => 'Fixed Deposit (FDR)',
                'category' => 'Fixed Return',
                'description' => 'Fixed Deposit Receipt - bank or financial institution',
                'default_tenure_months' => 12,
                'default_return_type' => 'fixed',
                'requires_approval' => false,
                'min_investment_amount' => 10000,
                'max_investment_amount' => 50000000,
                'features' => ['auto_reinvest'],
            ],
            [
                'code' => 'DPS',
                'name' => 'Deposit Pension Scheme (DPS)',
                'category' => 'Fixed Return',
                'description' => 'Monthly deposit scheme with fixed returns',
                'default_tenure_months' => 60,
                'default_return_type' => 'fixed',
                'requires_approval' => false,
                'min_investment_amount' => 1000,
                'max_investment_amount' => 1000000,
                'features' => ['auto_reinvest'],
            ],
            [
                'code' => 'SAVINGS_SCHEME',
                'name' => 'Savings Scheme',
                'category' => 'Fixed Return',
                'description' => 'Structured savings scheme with returns',
                'default_tenure_months' => 36,
                'default_return_type' => 'fixed',
                'requires_approval' => false,
                'min_investment_amount' => 5000,
                'max_investment_amount' => 5000000,
                'features' => ['auto_reinvest', 'flexible_maturity'],
            ],
            [
                'code' => 'OTHER',
                'name' => 'Other Investment',
                'category' => 'Miscellaneous',
                'description' => 'Other investment opportunities',
                'default_tenure_months' => 24,
                'default_return_type' => 'variable',
                'requires_approval' => false,
                'min_investment_amount' => null,
                'max_investment_amount' => null,
                'features' => [],
            ],
        ];

        foreach ($types as $type) {
            InvestmentType::firstOrCreate(
                ['code' => $type['code']],
                array_merge($type, [
                    'id' => Str::uuid(),
                    'is_active' => true,
                ])
            );
        }
    }
}
