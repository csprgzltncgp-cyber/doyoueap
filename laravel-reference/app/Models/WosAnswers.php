<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\WosAnswers
 *
 * @property int $id
 * @property int $case_id
 * @property int $answer_1
 * @property int $answer_2
 * @property int $answer_3
 * @property int $answer_4
 * @property int $answer_5
 * @property int $answer_6
 * @property string $created_at
 * @property-read Cases|null $wos_case
 *
 * @method static Builder|WosAnswers newModelQuery()
 * @method static Builder|WosAnswers newQuery()
 * @method static Builder|WosAnswers query()
 * @method static Builder|WosAnswers whereAnswer1($value)
 * @method static Builder|WosAnswers whereAnswer2($value)
 * @method static Builder|WosAnswers whereAnswer3($value)
 * @method static Builder|WosAnswers whereAnswer4($value)
 * @method static Builder|WosAnswers whereAnswer5($value)
 * @method static Builder|WosAnswers whereAnswer6($value)
 * @method static Builder|WosAnswers whereCaseId($value)
 * @method static Builder|WosAnswers whereCreatedAt($value)
 * @method static Builder|WosAnswers whereId($value)
 *
 * @mixin \Eloquent
 */
class WosAnswers extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wos_case(): HasOne
    {
        return $this->hasOne(Cases::class, 'id', 'case_id');
    }

    public static function addWosAnswers($request): array
    {
        $case = Cases::query()->findOrFail($request->case_id);

        if (self::query()->where('case_id', $request->case_id)->count() === 2) {
            return ['max_wos_per_case' => true];
        }

        if ($case->consultations->count()) {
            self::query()->create(array_merge($request->answers, [
                'case_id' => $case->id,
                'company_id' => $case->company_id,
                'country_id' => $case->country_id,
            ]));
        } else {
            return ['invalid_wos_save' => true];
        }

        return [
            'count' => self::query()->where('case_id', $request->case_id)->count(),
            'more_consultation_can_be_added' => (bool) self::query()->where('case_id', $request->case_id)->count(),
        ];
    }
}
