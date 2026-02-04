<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Asset
 *
 * @property int $id
 * @property int $owner_id
 * @property int $asset_type_id
 * @property string $own_id
 * @property string $cgp_id
 * @property string $name
 * @property Carbon $date_of_purchase
 * @property string|null $discard_reason
 * @property string|null $recycling_method
 * @property string|null $phone_num
 * @property string|null $pin
 * @property string|null $puk
 * @property string|null $provider
 * @property string|null $package
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read AssetOwner|null $owner
 * @property-read AssetType|null $asset_type
 *
 * @method static Builder|Asset newModelQuery()
 * @method static Builder|Asset newQuery()
 * @method static Builder|Asset onlyTrashed()
 * @method static Builder|Asset query()
 * @method static Builder|Asset whereCgpId($value)
 * @method static Builder|Asset whereCreatedAt($value)
 * @method static Builder|Asset whereDateOfPurchase($value)
 * @method static Builder|Asset whereDeletedAt($value)
 * @method static Builder|Asset whereDiscardReason($value)
 * @method static Builder|Asset whereId($value)
 * @method static Builder|Asset whereInventoryTypeId($value)
 * @method static Builder|Asset whereName($value)
 * @method static Builder|Asset whereOwnId($value)
 * @method static Builder|Asset whereOwnerId($value)
 * @method static Builder|Asset wherePackage($value)
 * @method static Builder|Asset wherePhoneNum($value)
 * @method static Builder|Asset wherePin($value)
 * @method static Builder|Asset whereProvider($value)
 * @method static Builder|Asset whereRecyclingMethod($value)
 * @method static Builder|Asset whereUpdatedAt($value)
 * @method static Builder|Asset withTrashed()
 * @method static Builder|Asset withoutTrashed()
 * @method static Builder|Asset whereAssetTypeId($value)
 *
 * @mixin \Eloquent
 */
class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['owner_id', 'asset_type_id', 'date_of_purchase', 'cgp_id', 'discard_reason', 'recycling_method'];

    protected $connection = 'mysql';

    protected $casts = [
        'date_of_purchase' => 'date:Y-m-d',
        'deleted_at' => 'date:Y-m-d',
    ];

    public function owner(): HasOne
    {
        return $this->hasOne(AssetOwner::class, 'id', 'owner_id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(AssetType::class, 'id', 'asset_type_id');
    }
}
