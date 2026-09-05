<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // Paramètres par défaut (ignorés si déjà présents)
        $defaults = [
            'hospital_name' => 'Hôpital de Sétif',
            'service_name'  => 'Réanimation médicale',
            'nb_beds'       => '20',
            'services'      => "Cardiologie\nGynécologie\nHématologie\nMaladies infectieuses\nMédecine interne\nMédecine légale\nNéonatologie\nNeurologie\nPédiatrie\nPneumologie\nUrgences",
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insertOrIgnore([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
