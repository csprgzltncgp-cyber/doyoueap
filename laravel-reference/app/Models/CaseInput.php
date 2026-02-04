<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\CaseInput
 *
 * @property int $id
 * @property string $name Az input neve egy kiválasztott nyelven  [ez csak az adminban jelenik meg]
 * @property int|null $company_id Megadja, hogy melyik céghez tartozik az adott input; ha null, akkor mindegyikhez
 * @property string|null $default_type
 * @property string|null $input_id Arra kell, hogy azonosítani tudjunk bizonyos inputokat
 * @property string $type
 * @property string $display_format Milyen formában jelenítjük meg a riportnál?
 * @property int $chart Kell-e diagram a riportnál?
 * @property bool $delete_later
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Translation|null $allTranslations
 * @property-read Translation|null $translation
 * @property-read Company|null $company
 * @property-read ContractHolderInput|null $selection
 * @property-read Collection<int, CaseInputValue> $values
 * @property-read int|null $values_count
 *
 * @method static Builder|CaseInput newModelQuery()
 * @method static Builder|CaseInput newQuery()
 * @method static Builder|CaseInput onlyTrashed()
 * @method static Builder|CaseInput query()
 * @method static Builder|CaseInput whereChart($value)
 * @method static Builder|CaseInput whereCompanyId($value)
 * @method static Builder|CaseInput whereCreatedAt($value)
 * @method static Builder|CaseInput whereDefaultType($value)
 * @method static Builder|CaseInput whereDeleteLater($value)
 * @method static Builder|CaseInput whereDeletedAt($value)
 * @method static Builder|CaseInput whereDisplayFormat($value)
 * @method static Builder|CaseInput whereId($value)
 * @method static Builder|CaseInput whereInputId($value)
 * @method static Builder|CaseInput whereName($value)
 * @method static Builder|CaseInput whereType($value)
 * @method static Builder|CaseInput whereUpdatedAt($value)
 * @method static Builder|CaseInput withTrashed()
 * @method static Builder|CaseInput withoutTrashed()
 *
 * @mixin \Eloquent
 */
class CaseInput extends Model
{
    use SoftDeletes;

    protected $table = 'case_inputs';

    protected $casts = [
        'delete_later' => 'boolean',
    ];

    public function translation(): MorphOne
    {
        // Try to return translation relation instance matching the user's language
        $translation = $this->morphOne(Translation::class, 'translatable')->where('language_id', Auth::user()->language_id);

        if ($translation->exists()) {
            return $translation;
        }

        // Try to return translation relation instance in english (language_id - 3)
        $translation = $this->morphOne(Translation::class, 'translatable')->where('language_id', 3);
        if ($translation->exists()) {
            return $translation;
        }

        // Return oldest exsiting instance
        return $this->morphOne(Translation::class, 'translatable')->oldest();
    }

    public function selection(): MorphOne
    {
        return $this->morphOne(ContractHolderInput::class, 'selectable');
    }

    public function allTranslations(): MorphOne
    {
        return $this->morphOne(Translation::class, 'translatable')->select('value', 'id', 'language_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(CaseInputValue::class)->select('id', 'value', 'case_input_id', 'is_default', 'visible', 'permission_id', 'contract_holder_id');
    }
}
