<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Type
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Content> $contents
 * @property-read int|null $contents_count
 *
 * @method static Builder|Type newModelQuery()
 * @method static Builder|Type newQuery()
 * @method static Builder|Type query()
 * @method static Builder|Type whereCreatedAt($value)
 * @method static Builder|Type whereId($value)
 * @method static Builder|Type whereName($value)
 * @method static Builder|Type whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Type extends Model
{
    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_types';

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
