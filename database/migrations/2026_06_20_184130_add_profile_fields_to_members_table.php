<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $columns = [
                // Personal
                'name_bn' => fn () => $table->string('name_bn')->nullable()->after('name'),
                'father_name' => fn () => $table->string('father_name')->nullable(),
                'mother_name' => fn () => $table->string('mother_name')->nullable(),
                'spouse_name' => fn () => $table->string('spouse_name')->nullable(),
                'date_of_birth' => fn () => $table->date('date_of_birth')->nullable(),
                'gender' => fn () => $table->string('gender', 20)->nullable(),
                'marital_status' => fn () => $table->string('marital_status', 20)->nullable(),
                'nationality' => fn () => $table->string('nationality', 100)->nullable(),
                // Identity
                'nid_number' => fn () => $table->string('nid_number', 50)->nullable(),
                'birth_registration' => fn () => $table->string('birth_registration', 50)->nullable(),
                'passport_number' => fn () => $table->string('passport_number', 50)->nullable(),
                'tax_id' => fn () => $table->string('tax_id', 50)->nullable(),
                // Contact
                'secondary_mobile' => fn () => $table->string('secondary_mobile', 20)->nullable(),
                'whatsapp_number' => fn () => $table->string('whatsapp_number', 20)->nullable(),
                // Present address
                'present_address_village' => fn () => $table->string('present_address_village')->nullable(),
                'present_address_po' => fn () => $table->string('present_address_po')->nullable(),
                'present_address_union' => fn () => $table->string('present_address_union')->nullable(),
                'present_address_upazila' => fn () => $table->string('present_address_upazila')->nullable(),
                'present_address_district' => fn () => $table->string('present_address_district')->nullable(),
                'present_address_postal' => fn () => $table->string('present_address_postal', 20)->nullable(),
                // Permanent address
                'same_as_permanent' => fn () => $table->boolean('same_as_permanent')->default(false),
                'permanent_address_village' => fn () => $table->string('permanent_address_village')->nullable(),
                'permanent_address_po' => fn () => $table->string('permanent_address_po')->nullable(),
                'permanent_address_union' => fn () => $table->string('permanent_address_union')->nullable(),
                'permanent_address_upazila' => fn () => $table->string('permanent_address_upazila')->nullable(),
                'permanent_address_district' => fn () => $table->string('permanent_address_district')->nullable(),
                'permanent_address_postal' => fn () => $table->string('permanent_address_postal', 20)->nullable(),
                // Professional
                'occupation' => fn () => $table->string('occupation')->nullable(),
                'business_name' => fn () => $table->string('business_name')->nullable(),
                'trade_license_number' => fn () => $table->string('trade_license_number', 100)->nullable(),
                'office_designation' => fn () => $table->string('office_designation')->nullable(),
                'employer_name' => fn () => $table->string('employer_name')->nullable(),
                'office_address' => fn () => $table->string('office_address', 500)->nullable(),
                // Files
                'photo_path' => fn () => $table->string('photo_path')->nullable(),
                'signature_path' => fn () => $table->string('signature_path')->nullable(),
            ];

            foreach ($columns as $name => $add) {
                if (! Schema::hasColumn('members', $name)) {
                    $add();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            foreach ([
                'name_bn', 'father_name', 'mother_name', 'spouse_name', 'date_of_birth',
                'gender', 'marital_status', 'nationality', 'nid_number', 'birth_registration',
                'passport_number', 'tax_id', 'secondary_mobile', 'whatsapp_number',
                'present_address_village', 'present_address_po', 'present_address_union',
                'present_address_upazila', 'present_address_district', 'present_address_postal',
                'same_as_permanent', 'permanent_address_village', 'permanent_address_po',
                'permanent_address_union', 'permanent_address_upazila', 'permanent_address_district',
                'permanent_address_postal', 'occupation', 'business_name', 'trade_license_number',
                'office_designation', 'employer_name', 'office_address', 'photo_path', 'signature_path',
            ] as $col) {
                if (Schema::hasColumn('members', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
