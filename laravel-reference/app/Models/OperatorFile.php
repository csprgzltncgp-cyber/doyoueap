<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\OperatorFile
 *
 * @property int $id
 * @property int $operator_data_id
 * @property string $filename
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OperatorData|null $operatorData
 *
 * @method static Builder|OperatorFile newModelQuery()
 * @method static Builder|OperatorFile newQuery()
 * @method static Builder|OperatorFile query()
 * @method static Builder|OperatorFile whereCreatedAt($value)
 * @method static Builder|OperatorFile whereFilename($value)
 * @method static Builder|OperatorFile whereId($value)
 * @method static Builder|OperatorFile whereOperatorDataId($value)
 * @method static Builder|OperatorFile wherePath($value)
 * @method static Builder|OperatorFile whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OperatorFile extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function ($file): void {
            Storage::delete(storage_path('app/'.$file->path));
        });
    }

    public function operatorData(): BelongsTo
    {
        return $this->belongsTo(OperatorData::class);
    }
}
