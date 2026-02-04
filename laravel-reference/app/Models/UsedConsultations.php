<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UsedConsultations
 *
 * @property int $id
 * @property int $cgp_employee
 * @property string $type
 * @property int|null $number_of_consultations
 * @property float|null $consultation_average
 * @property float|null $total_percentage
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|UsedConsultations newModelQuery()
 * @method static Builder|UsedConsultations newQuery()
 * @method static Builder|UsedConsultations query()
 * @method static Builder|UsedConsultations whereCgpEmployee($value)
 * @method static Builder|UsedConsultations whereConsultationAverage($value)
 * @method static Builder|UsedConsultations whereCreatedAt($value)
 * @method static Builder|UsedConsultations whereId($value)
 * @method static Builder|UsedConsultations whereNumberOfConsultations($value)
 * @method static Builder|UsedConsultations whereTotalPercentage($value)
 * @method static Builder|UsedConsultations whereType($value)
 * @method static Builder|UsedConsultations whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UsedConsultations extends Model
{
    use HasFactory;

    final public const TYPE_TOTAL_PERCENTAGE = 'percentage';

    final public const TYPE_AVERAGES = 'average';

    public $fillable = ['cgp_employee', 'type', 'number_of_consultations', 'consultation_average', 'total_percentage'];

    public function calculate_consultation_datas(): array
    {
        $used_consultation = static::query()->get();

        $averages = [];
        $percentages = [];

        foreach ($used_consultation->where('type', UsedConsultations::TYPE_TOTAL_PERCENTAGE) as $item) {
            $employee = ($item->cgp_employee) ? 'cgp' : 'affiliate';
            $percentages[$employee][] = $item->total_percentage;
        }

        foreach ($used_consultation->where('type', UsedConsultations::TYPE_AVERAGES) as $item) {
            $employee = ($item->cgp_employee) ? 'cgp' : 'affiliate';
            $averages[$employee][$item->number_of_consultations][] = $item->consultation_average;
        }

        $percentages['affiliate'] = round(array_sum($percentages['affiliate']) / count($percentages['affiliate']), 1);
        $percentages['cgp'] = round(array_sum($percentages['cgp']) / (is_countable($percentages['cgp']) ? count($percentages['cgp']) : 0), 1);

        foreach ($averages['cgp'] as $allowed => $values) {
            $averages['cgp'][$allowed] = round(array_sum($values) / count($values), 1);
        }

        $averages['cgp'] = collect($averages['cgp'])->sortKeys()->toArray();

        foreach ($averages['affiliate'] as $allowed => $values) {
            $averages['affiliate'][$allowed] = round(array_sum($values) / (is_countable($values) ? count($values) : 0), 1);
        }

        $averages['affiliate'] = collect($averages['affiliate'])->sortKeys()->toArray();

        $combined_data['percentage_sum'] = $percentages;
        $combined_data['averages'] = $averages;

        return $combined_data;
    }
}
