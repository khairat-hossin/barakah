<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the action_type enum to include expense-related actions
        DB::statement("ALTER TABLE audit_logs MODIFY action_type ENUM(
            'created', 'updated', 'deleted', 'restored', 'status_changed',
            'allocated', 'transfer_initiated', 'transfer_approved', 'transfer_rejected',
            'allocation_changed', 'position_assigned', 'position_changed', 'position_ended',
            'position_removed', 'uploaded', 'verified',
            'expense_created', 'expense_updated', 'expense_approved', 'expense_paid', 'attachment_uploaded'
        )");
    }

    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE audit_logs MODIFY action_type ENUM(
            'created', 'updated', 'deleted', 'restored', 'status_changed',
            'allocated', 'transfer_initiated', 'transfer_approved', 'transfer_rejected',
            'allocation_changed', 'position_assigned', 'position_changed', 'position_ended',
            'position_removed', 'uploaded', 'verified'
        )");
    }
};
