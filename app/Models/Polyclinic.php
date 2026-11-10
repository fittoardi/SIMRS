<?php

namespace App\Models;

use Database\Factories\PolyclinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Polyclinic extends Model
{
    /** @use HasFactory<PolyclinicFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function medicalActions(): HasMany
    {
        return $this->hasMany(MedicalAction::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
