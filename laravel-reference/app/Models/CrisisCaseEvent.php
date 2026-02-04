<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CrisisCaseEvent
 *
 * @property int $id
 * @property int $crisis_case_id Megadja, hogy melyik crisis case-hez tartozik
 * @property int|null $user_id Megadja, hogy ki oké-zta le ezt a jelzést
 * @property string $event
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CrisisCase|null $crisis_case
 *
 * @method static Builder|CrisisCaseEvent newModelQuery()
 * @method static Builder|CrisisCaseEvent newQuery()
 * @method static Builder|CrisisCaseEvent query()
 * @method static Builder|CrisisCaseEvent whereCreatedAt($value)
 * @method static Builder|CrisisCaseEvent whereCrisisCaseId($value)
 * @method static Builder|CrisisCaseEvent whereEvent($value)
 * @method static Builder|CrisisCaseEvent whereId($value)
 * @method static Builder|CrisisCaseEvent whereUpdatedAt($value)
 * @method static Builder|CrisisCaseEvent whereUserId($value)
 *
 * @mixin \Eloquent
 */
class CrisisCaseEvent extends Model
{
    protected $guarded = [];

    public function crisis_case(): BelongsTo
    {
        return $this->belongsTo(CrisisCase::class, 'crisis_case_id', 'id');
    }
}
