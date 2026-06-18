<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AttendanceLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'surat_tugas_id',
        'user_id',
        'attendance_date',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'check_in_photo_url',
        'check_out_at',
        'check_out_lat',
        'check_out_lng',
        'check_out_photo_url',
        'location_status',
        'approval_status',
        'approved_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_lat' => 'decimal:7',
            'check_in_lng' => 'decimal:7',
            'check_out_lat' => 'decimal:7',
            'check_out_lng' => 'decimal:7',
        ];
    }

    public function suratTugas(): BelongsTo
    {
        return $this->belongsTo(SuratTugas::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function meritTransaction(): MorphOne
    {
        return $this->morphOne(MeritTransaction::class, 'reference');
    }
}
