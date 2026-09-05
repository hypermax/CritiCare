<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'record_number',
        'first_name',
        'last_name',
        'birth_date',
        'sex_category',
        'phone',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function hospitalizations(): HasMany
    {
        return $this->hasMany(Hospitalization::class);
    }

    public function getFullNameAttribute(): string
    {
        return strtoupper($this->last_name) . ' ' . $this->first_name;
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }
}
