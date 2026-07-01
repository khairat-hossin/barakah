<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\InvestmentType;
use App\Models\OrganizationProfile;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Hash;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user (credentials configurable per server via .env)
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'System Administrator'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );

        // Create default chart of accounts (required for deposit/expense/investment
        // journal postings — without these the accounting observers silently skip)
        $accounts = [
            ['code' => '1100', 'name' => 'Cash', 'account_type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1200', 'name' => 'Bank', 'account_type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1300', 'name' => 'Investments', 'account_type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1400', 'name' => 'Receivables', 'account_type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1500', 'name' => 'Loans to Members', 'account_type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '2100', 'name' => 'Member Deposits', 'account_type' => 'LIABILITY', 'normal_balance' => 'CREDIT'],
            ['code' => '2200', 'name' => 'Payables', 'account_type' => 'LIABILITY', 'normal_balance' => 'CREDIT'],
            ['code' => '3100', 'name' => 'Share Capital', 'account_type' => 'EQUITY', 'normal_balance' => 'CREDIT'],
            ['code' => '3200', 'name' => 'Retained Earnings', 'account_type' => 'EQUITY', 'normal_balance' => 'CREDIT'],
            ['code' => '4100', 'name' => 'Investment Income', 'account_type' => 'INCOME', 'normal_balance' => 'CREDIT'],
            ['code' => '4200', 'name' => 'Other Income', 'account_type' => 'INCOME', 'normal_balance' => 'CREDIT'],
            ['code' => '5100', 'name' => 'Meeting Expenses', 'account_type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
            ['code' => '5200', 'name' => 'Office Expenses', 'account_type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
            ['code' => '5300', 'name' => 'Bank Charges', 'account_type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
            ['code' => '5400', 'name' => 'Miscellaneous Expenses', 'account_type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
                ['code' => $account['code']],
                [
                    'name' => $account['name'],
                    'account_type' => $account['account_type'],
                    'normal_balance' => $account['normal_balance'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );
        }

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
