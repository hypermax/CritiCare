<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Réparation unique : supprime une éventuelle table audit_logs
     * créée avec d'anciennes colonnes (sans auditable_type/auditable_id)
     * et la recrée avec le schéma complet. Sans effet si la table est saine.
     */
    public function up(): void
    {
        if (Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'auditable_type')) {
            return; // Table déjà correcte : rien à faire
        }

        Schema::dropIfExists('audit_logs');

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50)->index();
            $table->nullableMorphs('auditable');
            $table->string('description');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        // Volontairement vide : cette migration ne fait que réparer.
    }
};
