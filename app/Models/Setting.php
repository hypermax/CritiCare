<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Lit un paramètre (mis en cache jusqu'à modification). */
    public static function get(string $key, mixed $default = null): mixed
    {
        return cache()->rememberForever(
            "setting.{$key}",
            fn () => static::where('key', $key)->value('value') ?? $default
        );
    }

    /** Écrit un paramètre et invalide le cache. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting.{$key}");
    }

    /** Nombre de lits du service (défaut : 20). */
    public static function nbBeds(): int
    {
        return (int) static::get('nb_beds', 20);
    }

    /** Liste des services de provenance/destination (une ligne = un service). */
    public static function services(): array
    {
        return collect(preg_split('/\r?\n/', (string) static::get('services', '')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
