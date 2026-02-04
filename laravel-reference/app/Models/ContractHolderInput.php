<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContractHolderInput
 *
 * @property int $id
 * @property string $selectable_id
 * @property string $selectable_type
 * @property int $contract_holder_id
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model|\Eloquent $selectable
 *
 * @method static Builder|ContractHolderInput newModelQuery()
 * @method static Builder|ContractHolderInput newQuery()
 * @method static Builder|ContractHolderInput onlyTrashed()
 * @method static Builder|ContractHolderInput query()
 * @method static Builder|ContractHolderInput whereContractHolderId($value)
 * @method static Builder|ContractHolderInput whereCreatedAt($value)
 * @method static Builder|ContractHolderInput whereDeletedAt($value)
 * @method static Builder|ContractHolderInput whereId($value)
 * @method static Builder|ContractHolderInput whereSelectableId($value)
 * @method static Builder|ContractHolderInput whereSelectableType($value)
 * @method static Builder|ContractHolderInput whereSort($value)
 * @method static Builder|ContractHolderInput whereUpdatedAt($value)
 * @method static Builder|ContractHolderInput withTrashed()
 * @method static Builder|ContractHolderInput withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ContractHolderInput extends Model
{
    use SoftDeletes;

    protected $table = 'contract_holder_inputs';

    protected $fillable = ['selectable_type', 'selectable_id', 'contract_holder_id', 'sort'];

    public function translateCaseAttribute(): ?string
    {
        return static::attributeTranslation($this->selectable_id);
    }

    public static function attributeTranslation($value): ?string
    {
        return match ($value) {
            'id' => 'Case ID',
            'confirmed_at' => 'Case closed',
            'status' => 'Status',
            default => null,
        };
    }

    public static function getCaseAttributes(): array
    {
        return ['id', 'confirmed_at', 'status'];
    }

    public function translateCalculatedAttribute(): ?string
    {
        return static::calculatedAttributeTranslation($this->selectable_id);
    }

    public static function calculatedAttributeTranslation($value): ?string
    {
        return match ($value) {
            'session_provided' => 'Number of consultations',
            'session_dates' => 'Dates of consultations',
            'country' => 'Country',
            'orgID' => 'Org ID',
            'riport_subcategory' => 'Presenting concern',
            'first_consultation' => 'First consultation',
            'case_outcome' => 'Case outcome',
            'employee_type' => 'Employee type',
            default => null,
        };
    }

    public static function getCalculatedAttributes(): array
    {
        return ['session_provided', 'session_dates', 'orgID', 'country', 'riport_subcategory', 'first_consultation', 'case_outcome', 'employee_type'];
    }

    public function selectable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getCalculatedValue($case, $input)
    {
        $type = $input->selectable_id;
        if ($type == 'orgID') {
            return $case->company->orgId;
        }
        if ($type == 'session_provided') {
            return $case->consultations->count();
        }
        if ($type == 'riport_subcategory') {
            return $case->case_presenting_concern->input_value->riportSubCategory->translation->value ?? 'no data';
            // return $case->case_presenting_concern->input_value->riportSubCategory->translation->value;
        } elseif ($type == 'session_dates') {
            $value = '';
            foreach ($case->consultations as $key => $consultation) {
                $value .= \Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d');
                if ($key < $case->consultations->count() - 1) {
                    $value .= ', ';
                }
            }

            return $value;
        } elseif ($type == 'country') {
            return $case->country->code;
        } elseif ($type == 'first_consultation') {
            return $case->consultations->count() ? \Carbon\Carbon::parse($case->consultations->first()->created_at)->format('Y-m-d') : '';
        } elseif ($type == 'case_outcome') {
            if (in_array($case->getRawOriginal('status'), ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed'])) {
                return $case->status;
            }

            return 'In progress';
        } elseif ($type == 'employee_type') {
            return $case->case_client_language ? $case->case_client_language->value == '145' ? 'Expat' : 'Local' : '';
        }

        return null;
    }
}
