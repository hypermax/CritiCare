<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('hospitalizations', 'death_cause')) {
            return;
        }

        Schema::table('hospitalizations', function (Blueprint $table) {
            $table->text('death_cause')->nullable()->after('discharge_destination');
        });
    }

    public function down(): void
    {
        Schema::table('hospitalizations', function (Blueprint $table) {
            $table->dropColumn('death_cause');
        });
    }
};
