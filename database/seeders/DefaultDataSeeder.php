<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\InvestmentType;
use App\Models\OrganizationProfile;
use Illuminate\Support\Facades\Hash;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        User::firstOrCreate(
            ['email' => 'admin@barakah.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create default organization profile (empty, to be filled during setup)
        OrganizationProfile::firstOrCreate(
            [],
            [
                'organization_name_en' => null,
                'organization_name_bn' => null,
                'short_name' => null,
                'organization_type' => 'coop',
                'status' => 'active',
                'currency' => 'BDT',
                'country' => 'Bangladesh',
            ]
        );

        // Create expense categories
        $categories = [
            ['code' => 'utilities', 'name' => 'Utilities', 'description' => 'Electricity, water, gas'],
            ['code' => 'maintenance', 'name' => 'Maintenance', 'description' => 'Building and equipment maintenance'],
            ['code' => 'office_supplies', 'name' => 'Office Supplies', 'description' => 'Stationery and office supplies'],
            ['code' => 'travel', 'name' => 'Travel', 'description' => 'Transportation and travel expenses'],
            ['code' => 'meetings', 'name' => 'Meetings', 'description' => 'Meeting and event expenses'],
            ['code' => 'training', 'name' => 'Training', 'description' => 'Staff training and development'],
            ['code' => 'professional', 'name' => 'Professional Services', 'description' => 'Legal, accounting, consulting'],
            ['code' => 'other', 'name' => 'Other', 'description' => 'Miscellaneous expenses'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create payment methods
        $paymentMethods = [
            ['code' => 'cash', 'name' => 'Cash', 'description' => 'Payment in cash'],
            ['code' => 'bank_transfer', 'name' => 'Bank Transfer', 'description' => 'Bank wire transfer'],
            ['code' => 'mobile_banking', 'name' => 'Mobile Banking', 'description' => 'Mobile banking (bKash, Nagad, etc)'],
            ['code' => 'check', 'name' => 'Check', 'description' => 'Payment by check'],
            ['code' => 'other', 'name' => 'Other', 'description' => 'Other payment methods'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code']],
                [
                    'name' => $method['name'],
                    'description' => $method['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create investment types
        $investmentTypes = [
            ['code' => 'BUSINESS_INVESTMENT', 'name' => 'Business Investment'],
            ['code' => 'PARTNERSHIP', 'name' => 'Partnership'],
            ['code' => 'TRADING', 'name' => 'Trading'],
            ['code' => 'PROPERTY', 'name' => 'Property Investment'],
            ['code' => 'FIXED_DEPOSIT', 'name' => 'Fixed Deposit (FDR)'],
            ['code' => 'DPS', 'name' => 'Deposit Pension Scheme (DPS)'],
            ['code' => 'SAVINGS_SCHEME', 'name' => 'Savings Scheme'],
            ['code' => 'OTHER', 'name' => 'Other Investment'],
        ];

        foreach ($investmentTypes as $type) {
            InvestmentType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
