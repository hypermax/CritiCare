<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hospitalization extends Model
{
    protected $fillable = [
        'patient_id',
        'bed_number',
        'admission_dttm',
        'admission_diagnosis',
        'admission_source',
        'status',
        'discharge_dttm',
        'discharge_destination',
        'created_by',
    ];

    protected $casts = [
        'admission_dttm' => 'datetime',
        'discharge_dttm' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'Hospitalisé',
            'deceased'    => 'Décédé',
            'transferred' => 'Transféré',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'success',
            'deceased'    => 'danger',
            'transferred' => 'primary',
            default       => 'secondary',
        };
    }
}
