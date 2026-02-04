<?php

namespace App\Models\CompanyWebsite;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyWebsite\Translation
 *
 * @property int $id
 * @property int $translatable_id
 * @property string $translatable_type
 * @property int $language_id
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Language|null $language
 * @property-read Model|\Eloquent $translatable
 *
 * @method static Builder|Translation newModelQuery()
 * @method static Builder|Translation newQuery()
 * @method static Builder|Translation query()
 * @method static Builder|Translation whereCreatedAt($value)
 * @method static Builder|Translation whereId($value)
 * @method static Builder|Translation whereLanguageId($value)
 * @method static Builder|Translation whereTranslatableId($value)
 * @method static Builder|Translation whereTranslatableType($value)
 * @method static Builder|Translation whereUpdatedAt($value)
 * @method static Builder|Translation whereValue($value)
 *
 * @mixin \Eloquent
 */
class Translation extends Model
{
    protected $connection = 'mysql_company_website';

    protected $fillable = [
        'language_id',
        'translatable_id',
        'translatable_type',
        'value',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
