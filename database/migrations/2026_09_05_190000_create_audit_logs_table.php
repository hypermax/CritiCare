<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Garde-fou : ne rien faire si la table existe déjà
        // (la migration de réparation 2026_09_05_185000 l'a peut-être recréée)
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50)->index();          // ex. admission.created, discharge.deceased
            $table->nullableMorphs('auditable');            // objet concerné (Patient, Hospitalization, User…)
            $table->string('description');                  // phrase lisible en français
            $table->json('properties')->nullable();         // contexte (IPP, lit, rôle…)
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
