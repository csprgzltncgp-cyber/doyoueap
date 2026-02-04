<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\AssetOwner
 *
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Asset> $assets
 * @property-read int|null $inventory_items_count
 *
 * @method static Builder|AssetOwner newModelQuery()
 * @method static Builder|AssetOwner newQuery()
 * @method static Builder|AssetOwner query()
 * @method static Builder|AssetOwner whereCountryId($value)
 * @method static Builder|AssetOwner whereCreatedAt($value)
 * @method static Builder|AssetOwner whereId($value)
 * @method static Builder|AssetOwner whereName($value)
 * @method static Builder|AssetOwner whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AssetOwner extends Model
{
    use HasFactory;

    protected $fillable = ['id'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'owner_id');
    }
}
