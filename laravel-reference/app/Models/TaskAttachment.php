<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TaskAttachment
 *
 * @property int $id
 * @property int $task_id
 * @property string $filename
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task|null $task
 *
 * @method static Builder|TaskAttachment newModelQuery()
 * @method static Builder|TaskAttachment newQuery()
 * @method static Builder|TaskAttachment query()
 * @method static Builder|TaskAttachment whereCreatedAt($value)
 * @method static Builder|TaskAttachment whereFilename($value)
 * @method static Builder|TaskAttachment whereId($value)
 * @method static Builder|TaskAttachment wherePath($value)
 * @method static Builder|TaskAttachment whereTaskId($value)
 * @method static Builder|TaskAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TaskAttachment extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($attachment): void {
            if (file_exists($attachment->path)) {
                unlink($attachment->path);
            }
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
