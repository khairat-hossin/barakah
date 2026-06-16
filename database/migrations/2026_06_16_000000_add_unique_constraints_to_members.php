<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DELETE m1 FROM members m1 JOIN members m2 WHERE m1.id > m2.id AND m1.email = m2.email AND m1.email IS NOT NULL');
        DB::statement('DELETE m1 FROM members m1 JOIN members m2 WHERE m1.id > m2.id AND m1.phone = m2.phone AND m1.phone IS NOT NULL');

        Schema::table('members', function (Blueprint $table) {
            $table->unique('email')->change();
            $table->unique('phone')->change();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['phone']);
        });
    }
};
