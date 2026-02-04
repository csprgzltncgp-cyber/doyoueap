<?php

namespace App\Models;

use App\Scopes\CountryScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\City
 *
 * @property int $id
 * @property string $name
 * @property int $country_id Megadja, hogy melyik országhoz tartozik
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Country $country
 * @property-read Collection<int, User> $experts
 * @property-read int|null $experts_count
 *
 * @method static Builder|City newModelQuery()
 * @method static Builder|City newQuery()
 * @method static Builder|City onlyTrashed()
 * @method static Builder|City query()
 * @method static Builder|City whereCountryId($value)
 * @method static Builder|City whereCreatedAt($value)
 * @method static Builder|City whereDeletedAt($value)
 * @method static Builder|City whereId($value)
 * @method static Builder|City whereName($value)
 * @method static Builder|City whereUpdatedAt($value)
 * @method static Builder|City withTrashed()
 * @method static Builder|City withoutTrashed()
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CountryScope);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function experts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_x_city', 'city_id', 'user_id');
    }

    public static function add($request): void
    {
        $document = new self;
        $document->name = $request->name;
        $document->country_id = $request->country_id;
        $document->save();
    }

    public static function edit($id, $request): void
    {
        $document = self::query()->findOrFail($id);
        $document->name = $request->name;
        $document->country_id = $request->country_id;
        $document->save();
    }
}
