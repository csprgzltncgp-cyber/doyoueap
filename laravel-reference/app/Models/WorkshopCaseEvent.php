<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\WorkshopCaseEvent
 *
 * @property int $id
 * @property int $workshop_case_id Megadja, hogy melyik workshop case-hez tartozik
 * @property int|null $user_id Megadja, hogy ki oké-zta le ezt a jelzést
 * @property string $event
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read WorkshopCase|null $workshop_case
 *
 * @method static Builder|WorkshopCaseEvent newModelQuery()
 * @method static Builder|WorkshopCaseEvent newQuery()
 * @method static Builder|WorkshopCaseEvent query()
 * @method static Builder|WorkshopCaseEvent whereCreatedAt($value)
 * @method static Builder|WorkshopCaseEvent whereDeletedAt($value)
 * @method static Builder|WorkshopCaseEvent whereEvent($value)
 * @method static Builder|WorkshopCaseEvent whereId($value)
 * @method static Builder|WorkshopCaseEvent whereUpdatedAt($value)
 * @method static Builder|WorkshopCaseEvent whereUserId($value)
 * @method static Builder|WorkshopCaseEvent whereWorkshopCaseId($value)
 *
 * @mixin \Eloquent
 */
class WorkshopCaseEvent extends Model
{
    protected $guarded = [];

    public function workshop_case(): BelongsTo
    {
        return $this->belongsTo(WorkshopCase::class, 'workshop_case_id', 'id');
    }
}
