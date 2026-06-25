<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLocation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'location_name',
        'client_name',
        'address',
        'latitude',
        'longitude',
        'radius_tolerance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
