<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ExpertFile
 *
 * @property int $id
 * @property int $expert_data_id
 * @property string $filename
 * @property string $path
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ExpertData|null $expertData
 *
 * @method static Builder|ExpertFile newModelQuery()
 * @method static Builder|ExpertFile newQuery()
 * @method static Builder|ExpertFile query()
 * @method static Builder|ExpertFile whereCreatedAt($value)
 * @method static Builder|ExpertFile whereExpertDataId($value)
 * @method static Builder|ExpertFile whereFilename($value)
 * @method static Builder|ExpertFile whereId($value)
 * @method static Builder|ExpertFile wherePath($value)
 * @method static Builder|ExpertFile whereType($value)
 * @method static Builder|ExpertFile whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ExpertFile extends Model
{
    final public const TYPE_CONTRACT = 1;

    final public const TYPE_CERTIFICATE = 2;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function ($file): void {
            unlink(storage_path('app/'.$file->path));
        });
    }

    public function expertData(): BelongsTo
    {
        return $this->belongsTo(ExpertData::class);
    }
}
