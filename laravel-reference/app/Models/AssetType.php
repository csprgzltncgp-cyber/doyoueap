<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\AssetType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Asset|null $asset_type
 *
 * @method static Builder|AssetType newModelQuery()
 * @method static Builder|AssetType newQuery()
 * @method static Builder|AssetType query()
 * @method static Builder|AssetType whereCreatedAt($value)
 * @method static Builder|AssetType whereId($value)
 * @method static Builder|AssetType whereName($value)
 * @method static Builder|AssetType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AssetType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function asset_type(): HasOne
    {
        return $this->hasOne(Asset::class, 'asset_type_id');
    }
}
