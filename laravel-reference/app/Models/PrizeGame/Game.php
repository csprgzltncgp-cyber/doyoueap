<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Game
 *
 * @property int $id
 * @property Carbon $from
 * @property Carbon $to
 * @property int $status
 * @property bool $is_viewable
 * @property int $content_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Content|null $content
 * @property-read Collection<int, Guess> $guesses
 * @property-read int|null $guesses_count
 * @property-read Collection<int, Winner> $winners
 * @property-read int|null $winners_count
 *
 * @method static Builder|Game newModelQuery()
 * @method static Builder|Game newQuery()
 * @method static Builder|Game onlyTrashed()
 * @method static Builder|Game query()
 * @method static Builder|Game whereContentId($value)
 * @method static Builder|Game whereCreatedAt($value)
 * @method static Builder|Game whereDeletedAt($value)
 * @method static Builder|Game whereFrom($value)
 * @method static Builder|Game whereId($value)
 * @method static Builder|Game whereIsViewable($value)
 * @method static Builder|Game whereStatus($value)
 * @method static Builder|Game whereTo($value)
 * @method static Builder|Game whereUpdatedAt($value)
 * @method static Builder|Game withTrashed()
 * @method static Builder|Game withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Game extends Model
{
    use SoftDeletes;

    final public const STATUS_PENDING = 1;

    final public const STATUS_ACTIVE = 2;

    final public const STATUS_CLOSED = 3;

    final public const STATUS_DRAWN = 4;

    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_games';

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'is_viewable' => 'boolean',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $game): void {
            foreach ($game->guesses()->get() as $guess) {
                $guess->delete();
            }
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function guesses(): HasMany
    {
        return $this->hasMany(Guess::class);
    }

    public function winners(): HasManyThrough
    {
        return $this->hasManyThrough(Winner::class, Guess::class);
    }
}
