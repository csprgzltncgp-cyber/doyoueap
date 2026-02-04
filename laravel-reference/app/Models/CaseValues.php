<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\CaseValues
 *
 * @property int $id
 * @property int $case_id Megadja, hogy melyik esethez tartozik
 * @property int $case_input_id Megadja, hogy melyik case input-hoz tartozik
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Cases $case
 * @property-read City|null $city
 * @property-read CaseInput $input
 * @property-read CaseInputValue|null $input_value
 * @property-read Permission $permission
 * @property-read CaseInputValue|null $select_value
 *
 * @method static Builder|CaseValues newModelQuery()
 * @method static Builder|CaseValues newQuery()
 * @method static Builder|CaseValues query()
 * @method static Builder|CaseValues whereCaseId($value)
 * @method static Builder|CaseValues whereCaseInputId($value)
 * @method static Builder|CaseValues whereCreatedAt($value)
 * @method static Builder|CaseValues whereId($value)
 * @method static Builder|CaseValues whereUpdatedAt($value)
 * @method static Builder|CaseValues whereValue($value)
 *
 * @mixin \Eloquent
 */
class CaseValues extends Model
{
    protected $table = 'case_values';

    protected $fillable = ['case_id', 'case_input_id', 'value', 'company_id'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(Cases::class);
    }

    public function input(): BelongsTo
    {
        return $this->belongsTo(CaseInput::class, 'case_input_id');
    }

    public function input_value(): BelongsTo
    {
        return $this->belongsTo(CaseInputValue::class, 'value');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'value');
    }

    public function select_value(): BelongsTo
    {
        return $this->belongsTo(CaseInputValue::class, 'value');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'value');
    }

    public function showAbleAfter3Months(): bool
    {
        if (in_array($this->input->id, [5, 10, 2, 32, 11, 54])) {
            return optional($this->case)->created_at >= now()->subMonths(3);
        }
        if (! is_null($this->input->company_id)) {
            return optional($this->case)->created_at >= now()->subMonths(3);
        }

        return true;
    }

    public function getValue()
    {
        try {
            if (empty($this->input)) {
                return '';
            }

            if ($this->input->type != 'select') {
                return $this->value;
            }

            if ($this->input->default_type == 'company_chooser') {
                $company = Company::query()->find($this->value);

                return $company ? $company->name : '';
            }

            if ($this->input->default_type == 'case_type') {
                return optional(Permission::query()->findOrFail($this->value)->translation)->value;
            }

            if ($this->input->default_type == 'case_language_skill' && $this->value != '') {
                return optional(LanguageSkill::query()->findOrFail($this->value)->translation)->value;
            }

            if ($this->input->default_type == 'case_specialization' && $this->value != '') {
                return optional(Specialization::query()->findOrFail($this->value)->translation)->value;
            }

            if ($this->input->default_type == 'clients_language' && $this->value != '') {
                if (LanguageSkill::query()->where('id', $this->value)->exists()) {
                    return LanguageSkill::query()->where('id', $this->value)->first()->translation->value;
                }

                $old_value = CaseInputValue::query()->where('id', (int) $this->value)->first();

                return $old_value->translation->value;
            }

            if ($this->input->default_type == 'location') {
                if ($this->city !== null) {
                    return $this->city->name;
                }

                return '';
            }

            return $this->select_value != null && $this->select_value->translation != null ? $this->select_value->translation->value : '';

        } catch (Exception $e) {
            Log::info('ERROR trying to get case value for input:'.$this->input->id.' in case: '.$this->case->id.' Exception:'.$e->getMessage());
        }

        return null;
    }
}
