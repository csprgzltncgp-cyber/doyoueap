<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Specialization
 *
 * @property int $id
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Translation|null $translation
 *
 * @method static Builder|Specialization newModelQuery()
 * @method static Builder|Specialization newQuery()
 * @method static Builder|Specialization query()
 * @method static Builder|Specialization whereCreatedAt($value)
 * @method static Builder|Specialization whereDeletedAt($value)
 * @method static Builder|Specialization whereId($value)
 * @method static Builder|Specialization whereSlug($value)
 * @method static Builder|Specialization whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Specialization extends Model
{
    use HasFactory;

    public function translation(): MorphOne
    {
        $language_id = Auth::user() !== null ? Auth::user()->language_id : 3;

        return $this->morphOne(Translation::class, 'translatable')->where('language_id', $language_id)->select('value');
    }
}
