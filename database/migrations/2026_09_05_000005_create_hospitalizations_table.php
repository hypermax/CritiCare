<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->restrictOnDelete();
            $table->string('bed_number', 10);                // Lit
            $table->dateTime('admission_dttm');              // CLIF: admission_dttm
            $table->string('admission_diagnosis')->nullable();
            $table->string('admission_source')->nullable();  // Urgences, domicile, transfert...
            $table->string('status', 20)->default('active'); // active | deceased | transferred
            $table->dateTime('discharge_dttm')->nullable();  // CLIF: discharge_dttm
            $table->string('discharge_destination')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizations');
    }
};
