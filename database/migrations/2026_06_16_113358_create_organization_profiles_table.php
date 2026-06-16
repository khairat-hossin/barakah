<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();

            // Section 1: General Information
            $table->string('organization_name_bn')->nullable(); // Bangla name
            $table->string('organization_name_en')->nullable(); // English name
            $table->string('short_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('seal_path')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->enum('organization_type', ['coop', 'ngo', 'mutual', 'association', 'other'])->default('coop');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // Contact Information
            $table->string('mobile_number')->nullable();
            $table->string('secondary_mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('whatsapp_number')->nullable();

            // Address
            $table->text('address_line')->nullable();
            $table->string('village_area')->nullable();
            $table->string('post_office')->nullable();
            $table->string('union_ward')->nullable();
            $table->string('upazila')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Bangladesh');

            // Section 2: Mission & Objectives
            $table->string('motto')->nullable();
            $table->text('vision_statement')->nullable();
            $table->text('mission_statement')->nullable();
            $table->json('core_values')->nullable(); // Array of values
            $table->json('objectives')->nullable(); // Array of objectives
            $table->longText('about_organization')->nullable();

            // Section 3: Share Structure
            $table->integer('total_shares')->default(0);
            $table->decimal('share_face_value', 15, 2)->default(0);
            $table->string('currency')->default('BDT');
            $table->enum('share_ownership_model', ['individual', 'collective', 'hybrid'])->default('individual');
            $table->boolean('share_transfer_allowed')->default(true);
            $table->boolean('partial_share_transfer_allowed')->default(false);
            $table->integer('minimum_shares_per_member')->default(1);
            $table->integer('maximum_shares_per_member')->nullable();

            // Section 4: Membership Rules
            $table->enum('membership_type', ['share_based', 'open', 'invitation_only'])->default('share_based');
            $table->boolean('new_member_admission_allowed')->default(true);
            $table->boolean('membership_approval_required')->default(false);
            $table->integer('minimum_share_requirement')->default(1);
            $table->integer('maximum_share_ownership')->nullable();
            $table->boolean('allow_membership_transfer')->default(false);

            // Section 5: Committee Structure
            $table->integer('committee_term_length')->default(3); // in years
            $table->integer('maximum_consecutive_terms')->default(2);
            $table->boolean('election_required')->default(true);
            $table->boolean('re_election_allowed')->default(true);
            $table->json('committee_positions')->nullable(); // Array of positions

            // Section 6: Financial Configuration
            $table->string('default_currency')->default('BDT');
            $table->decimal('reserve_fund_percentage', 5, 2)->default(10);
            $table->decimal('emergency_fund_percentage', 5, 2)->default(5);

            // Banking Information
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();

            // Contribution Settings
            $table->decimal('membership_fee', 15, 2)->nullable();
            $table->decimal('share_purchase_fee', 15, 2)->nullable();
            $table->decimal('annual_contribution', 15, 2)->nullable();
            $table->decimal('special_contribution', 15, 2)->nullable();

            // Section 7: Meeting Rules
            $table->integer('general_meeting_notice_days')->default(7);
            $table->integer('general_meeting_quorum_percentage')->default(50);
            $table->enum('general_meeting_voting_method', ['majority', 'unanimous', 'consensus'])->default('majority');

            $table->integer('committee_meeting_notice_days')->default(3);
            $table->integer('committee_meeting_quorum_percentage')->default(50);
            $table->integer('minimum_committee_meetings_per_year')->default(12);

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
