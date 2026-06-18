<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardCatalog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'point_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function rewardRequests(): HasMany
    {
        return $this->hasMany(RewardRequest::class);
    }
}
