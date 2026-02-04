<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelusCaseCode extends Model
{
    protected $fillable = ['case_id', 'code', 'file', 'downloaded_at'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(Cases::class);
    }
}
