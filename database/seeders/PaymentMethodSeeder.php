<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'code' => 'cash', 'description' => 'Payment made in cash', 'is_active' => true],
            ['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'description' => 'Payment via bank transfer', 'is_active' => true],
            ['name' => 'Mobile Banking', 'code' => 'mobile_banking', 'description' => 'Payment via mobile banking app', 'is_active' => true],
            ['name' => 'Check', 'code' => 'check', 'description' => 'Payment via check', 'is_active' => true],
            ['name' => 'Other', 'code' => 'other', 'description' => 'Other payment method', 'is_active' => true],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
