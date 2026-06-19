<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('savings_entries', function (Blueprint $table) {
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('savings_entries', function (Blueprint $table) {
            $table->dropForeignKey('savings_entries_payment_method_id_foreign');
        });
    }
};
