<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $slug Csak segítség, hogy melyik jogosultság, de a fordítása a translations táblában található
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Translation|null $translation
 *
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission onlyTrashed()
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereDeletedAt($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereSlug($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @method static Builder|Permission withTrashed()
 * @method static Builder|Permission withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $guarded = [];

    public function translation(): MorphOne
    {
        $language_id = Auth::user() !== null ? Auth::user()->language_id : 3;

        return $this->morphOne(Translation::class, 'translatable')->where('language_id', $language_id)->select('value');
    }
}
