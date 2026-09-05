<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    // Journal en écriture seule : created_at est géré par la base, jamais de mise à jour
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Enregistre une entrée dans le journal d'audit.
     */
    public static function record(
        string $action,
        string $description,
        ?Model $auditable = null,
        array $properties = [],
        ?int $userId = null
    ): self {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
        ]);
    }

    /** Libellé français de l'action, pour l'affichage. */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'auth.login' => 'Connexion',
            'auth.logout' => 'Déconnexion',
            'patient.created' => 'Nouveau patient',
            'admission.created' => 'Admission',
            'discharge.transferred' => 'Sortie (transfert)',
            'discharge.deceased' => 'Décès',
            'user.created' => 'Compte créé',
            'user.role_changed' => 'Rôle modifié',
            'user.password_reset' => 'Mot de passe réinitialisé',
            'user.updated' => 'Compte modifié',
            default => $this->action,
        };
    }
}
