<?php

namespace App\Models;

use App\Traits\LiveWebinarActivityId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LiveWebinar extends Model
{
    use HasFactory;
    use LiveWebinarActivityId;

    protected $fillable = [
        'permission_id',
        'topic',
        'user_id',
        'from',
        'to',
        'duration',
        'description',
        'language_id', // EAP Language
        'zoom_meeting_id',
        'zoom_meeting_uuid',
        'zoom_host_start_url',
        'zoom_join_url',
        'zoom_passcode',
        'zoom_sdk_role',
        'zoom_meeting_started_at',
        'zoom_meeting_ended_at',
        'recording_status',
        'vimeo_video_url',
        'recording_archived_at',
    ];

    protected $casts = [
        'eap_languages' => 'array',
        'from' => 'datetime',
        'to' => 'datetime',
        'zoom_meeting_started_at' => 'datetime',
        'zoom_meeting_ended_at' => 'datetime',
        'recording_archived_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function ($model): void {
            $model->activity_id = $model->generateActivityId();
        });
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_x_live_webinar', 'live_webinar_id', 'company_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_x_live_webinar', 'live_webinar_id', 'country_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function invoice_live_webinar_data(): BelongsTo
    {
        return $this->belongsTo(InvoiceLiveWebinarData::class, 'id', 'live_webinar_id');
    }
}
