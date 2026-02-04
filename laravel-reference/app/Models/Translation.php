<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Translation
 *
 * @property int $id
 * @property string $value
 * @property int $language_id Megadja, hogy melyik nyelvhez tartozik az adott fordítás
 * @property int $translatable_id
 * @property string $translatable_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model|\Eloquent $translatable
 *
 * @method static Builder|Translation newModelQuery()
 * @method static Builder|Translation newQuery()
 * @method static Builder|Translation onlyTrashed()
 * @method static Builder|Translation query()
 * @method static Builder|Translation whereCreatedAt($value)
 * @method static Builder|Translation whereDeletedAt($value)
 * @method static Builder|Translation whereId($value)
 * @method static Builder|Translation whereLanguageId($value)
 * @method static Builder|Translation whereTranslatableId($value)
 * @method static Builder|Translation whereTranslatableType($value)
 * @method static Builder|Translation whereUpdatedAt($value)
 * @method static Builder|Translation whereValue($value)
 * @method static Builder|Translation withTrashed()
 * @method static Builder|Translation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Translation extends Model
{
    use SoftDeletes;

    protected $fillable = ['value', 'language_id', 'translatable_id', 'translatable_type'];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
