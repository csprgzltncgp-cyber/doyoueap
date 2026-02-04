<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Winner
 *
 * @property int $id
 * @property int $guess_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Guess|null $guess
 *
 * @method static Builder|Winner newModelQuery()
 * @method static Builder|Winner newQuery()
 * @method static Builder|Winner query()
 * @method static Builder|Winner whereCreatedAt($value)
 * @method static Builder|Winner whereGuessId($value)
 * @method static Builder|Winner whereId($value)
 * @method static Builder|Winner whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Winner extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_winners';

    protected $guarded = [];

    public function guess(): BelongsTo
    {
        return $this->belongsTo(Guess::class);
    }
}
