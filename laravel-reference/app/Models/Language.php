<?php

namespace App\Models;

use App\Scopes\LanguageScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $code pl. hu, en, de
 * @property string $name pl. Magyar, English, Deutsch
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language onlyTrashed()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCode($value)
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereDeletedAt($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereName($value)
 * @method static Builder|Language whereUpdatedAt($value)
 * @method static Builder|Language withTrashed()
 * @method static Builder|Language withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Language extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new LanguageScope);
    }
}
