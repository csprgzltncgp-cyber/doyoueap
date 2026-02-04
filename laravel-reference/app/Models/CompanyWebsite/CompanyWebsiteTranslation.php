<?php

namespace App\Models\CompanyWebsite;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyWebsite\CompanyWebsiteTranslation
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
 * @method static Builder|CompanyWebsiteTranslation newModelQuery()
 * @method static Builder|CompanyWebsiteTranslation newQuery()
 * @method static Builder|CompanyWebsiteTranslation query()
 * @method static Builder|CompanyWebsiteTranslation whereCreatedAt($value)
 * @method static Builder|CompanyWebsiteTranslation whereId($value)
 * @method static Builder|CompanyWebsiteTranslation whereLanguageId($value)
 * @method static Builder|CompanyWebsiteTranslation whereTranslatableId($value)
 * @method static Builder|CompanyWebsiteTranslation whereTranslatableType($value)
 * @method static Builder|CompanyWebsiteTranslation whereUpdatedAt($value)
 * @method static Builder|CompanyWebsiteTranslation whereValue($value)
 *
 * @mixin \Eloquent
 */
class CompanyWebsiteTranslation extends Model
{
    protected $connection = 'mysql_company_website';

    protected $table = 'translations';

    protected $guarded = [];

    public function translatable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
