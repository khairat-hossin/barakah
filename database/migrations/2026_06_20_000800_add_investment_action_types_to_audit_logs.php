<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the action_type enum to include investment-related actions
        DB::statement("ALTER TABLE audit_logs MODIFY action_type ENUM(
            'created', 'updated', 'deleted', 'restored', 'status_changed',
            'allocated', 'transfer_initiated', 'transfer_approved', 'transfer_rejected',
            'allocation_changed', 'position_assigned', 'position_changed', 'position_ended',
            'position_removed', 'uploaded', 'verified',
            'expense_created', 'expense_updated', 'expense_approved', 'expense_paid', 'attachment_uploaded',
            'investment_created', 'investment_updated', 'investment_status_changed',
            'investment_transaction_created', 'investment_transaction_approved', 'investment_transaction_reversed',
            'investment_document_uploaded', 'investment_document_verified'
        )");
    }

    public function down(): void
    {
        // Revert to previous enum values
        DB::statement("ALTER TABLE audit_logs MODIFY action_type ENUM(
            'created', 'updated', 'deleted', 'restored', 'status_changed',
            'allocated', 'transfer_initiated', 'transfer_approved', 'transfer_rejected',
            'allocation_changed', 'position_assigned', 'position_changed', 'position_ended',
            'position_removed', 'uploaded', 'verified',
            'expense_created', 'expense_updated', 'expense_approved', 'expense_paid', 'attachment_uploaded'
        )");
    }
};
