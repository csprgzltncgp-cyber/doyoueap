<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Guess
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $case_id
 * @property int $game_id
 * @property int $valid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Game|null $game
 * @property-read Collection<int, Photo> $photos
 * @property-read int|null $photos_count
 * @property-read Winner|null $winner
 *
 * @method static Builder|Guess newModelQuery()
 * @method static Builder|Guess newQuery()
 * @method static Builder|Guess query()
 * @method static Builder|Guess whereCaseId($value)
 * @method static Builder|Guess whereCreatedAt($value)
 * @method static Builder|Guess whereEmail($value)
 * @method static Builder|Guess whereFirstName($value)
 * @method static Builder|Guess whereGameId($value)
 * @method static Builder|Guess whereId($value)
 * @method static Builder|Guess whereLastName($value)
 * @method static Builder|Guess wherePhone($value)
 * @method static Builder|Guess whereUpdatedAt($value)
 * @method static Builder|Guess whereUsername($value)
 * @method static Builder|Guess whereValid($value)
 *
 * @property string|null $end_screen_seen_at
 *
 * @method static Builder|Guess whereEndScreenSeenAt($value)
 *
 * @mixin \Eloquent
 */
class Guess extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_guesses';

    protected $fillable = [];

    protected $casts = [
        'vaild' => 'boolean',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $guess): void {
            if ($guess->winner()->exists()) {
                $guess->winner()->delete();
            }
        });
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }
}
