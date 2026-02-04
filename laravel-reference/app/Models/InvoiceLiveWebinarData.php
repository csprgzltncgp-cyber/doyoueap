<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLiveWebinarData extends Model
{
    protected $table = 'invoice_live_webinar_datas';

    protected $fillable = [
        'live_webinar_id',
        'activity_id',
        'expert_id',
        'price',
        'currency',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'expert_id');
    }
}
