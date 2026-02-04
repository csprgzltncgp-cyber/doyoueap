<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\WorkshopFeedback
 *
 * @property int $id
 * @property int $workshop_case_id
 * @property int $question_1
 * @property int $question_2
 * @property int $question_3
 * @property int $question_4
 * @property int $question_5
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkshopCase|null $workshop_case
 *
 * @method static Builder|WorkshopFeedback newModelQuery()
 * @method static Builder|WorkshopFeedback newQuery()
 * @method static Builder|WorkshopFeedback query()
 * @method static Builder|WorkshopFeedback whereCreatedAt($value)
 * @method static Builder|WorkshopFeedback whereId($value)
 * @method static Builder|WorkshopFeedback whereQuestion1($value)
 * @method static Builder|WorkshopFeedback whereQuestion2($value)
 * @method static Builder|WorkshopFeedback whereQuestion3($value)
 * @method static Builder|WorkshopFeedback whereQuestion4($value)
 * @method static Builder|WorkshopFeedback whereQuestion5($value)
 * @method static Builder|WorkshopFeedback whereUpdatedAt($value)
 * @method static Builder|WorkshopFeedback whereWorkshopCaseId($value)
 *
 * @mixin \Eloquent
 */
class WorkshopFeedback extends Model
{
    protected $guarded = [];

    public function workshop_case(): BelongsTo
    {
        return $this->belongsTo(WorkshopCase::class, 'workshop_case_id', 'id');
    }
}
