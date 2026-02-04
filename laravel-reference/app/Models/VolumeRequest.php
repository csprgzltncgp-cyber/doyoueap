<?php

namespace App\Models;

use App\Enums\VolumeRequestStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolumeRequest extends Model
{
    protected $fillable = [
        'volume_id',
        'headcount',
        'date',
        'status',
        'email_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => VolumeRequestStatusEnum::class,
    ];

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }
}
