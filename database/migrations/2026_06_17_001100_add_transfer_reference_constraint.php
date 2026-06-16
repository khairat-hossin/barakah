<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_share_ownership', function (Blueprint $table) {
            $table->foreign('transfer_reference')
                ->references('id')
                ->on('share_transfers')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('member_share_ownership', function (Blueprint $table) {
            $table->dropForeign(['transfer_reference']);
        });
    }
};
