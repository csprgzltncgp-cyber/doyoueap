<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\CaseInputValue
 *
 * @property int $id
 * @property string $value Az érték
 * @property int $case_input_id Megadja, hogy melyik input-hoz tartozik az adott input érték
 * @property int|null $contract_holder_id
 * @property int|null $riport_category_id
 * @property int|null $riport_subcategory_id
 * @property int $is_default Megadja, hogy adott input érték-e a default érték megjelenítéskor
 * @property int $visible
 * @property string|null $icon
 * @property int|null $permission_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Translation|null $allTranslations
 * @property-read CaseInput $caseInput
 * @property-read Translation|null $translation
 *
 * @method static Builder|CaseInputValue newModelQuery()
 * @method static Builder|CaseInputValue newQuery()
 * @method static Builder|CaseInputValue onlyTrashed()
 * @method static Builder|CaseInputValue query()
 * @method static Builder|CaseInputValue whereCaseInputId($value)
 * @method static Builder|CaseInputValue whereContractHolderId($value)
 * @method static Builder|CaseInputValue whereCreatedAt($value)
 * @method static Builder|CaseInputValue whereDeletedAt($value)
 * @method static Builder|CaseInputValue whereIcon($value)
 * @method static Builder|CaseInputValue whereId($value)
 * @method static Builder|CaseInputValue whereIsDefault($value)
 * @method static Builder|CaseInputValue wherePermissionId($value)
 * @method static Builder|CaseInputValue whereRiportCategoryId($value)
 * @method static Builder|CaseInputValue whereRiportSubcategoryId($value)
 * @method static Builder|CaseInputValue whereUpdatedAt($value)
 * @method static Builder|CaseInputValue whereValue($value)
 * @method static Builder|CaseInputValue whereVisible($value)
 * @method static Builder|CaseInputValue withTrashed()
 * @method static Builder|CaseInputValue withoutTrashed()
 *
 * @mixin \Eloquent
 */
class CaseInputValue extends Model
{
    use SoftDeletes;

    protected $table = 'case_input_values';

    public function getIconAttribute(?string $value)
    {
        return asset('/assets/'.$value);
    }

    public function translation(): MorphOne
    {
        $language_id = Auth::user() !== null ? Auth::user()->language_id : 3;

        return $this->morphOne(Translation::class, 'translatable')->where('language_id', $language_id);
    }

    public function caseInput(): BelongsTo
    {
        return $this->belongsTo(CaseInput::class);
    }

    public function allTranslations(): MorphOne
    {
        return $this->morphOne(Translation::class, 'translatable')->select('value', 'id', 'language_id');
    }

    public static function edit($input_id, Request $request): void
    {
        $input_ids = [];
        $languages = Language::query()->get();
        // return $request->name;
        if ($request->name) {
            // régi inputok módoítása
            foreach ($request->name as $key => $value) {
                $array = [];

                $array['contract_holder_id'] = $request->contract_holder_id[$key];

                // ha magyar a felhasználó, akkor átírhatjuk az adminban megjelenő nevet is
                if (Auth::user()->language_id == 1) {
                    $array['value'] = $value;
                }

                if ($request->permission) {
                    $array['permission_id'] = $request->permission[$key];
                }

                self::query()->where('id', $key)->update($array);

                /* ADOTT INPUTHOZ TARTOZÓ FORDÍTÁSOK MENTÉSE */
                foreach ($languages as $language) {
                    if ($request->translations[$language->id][$key]) {
                        Translation::query()->updateOrCreate([
                            'language_id' => $language->id,
                            'translatable_type' => self::class,
                            'translatable_id' => $key,
                        ], [
                            'value' => $request->translations[$language->id][$key],
                        ]);
                    }
                }
                $input_ids[] = $key;
                /* ADOTT INPUTHOZ TARTOZÓ FORDÍTÁSOK MENTÉSE */
            }
        }
        // régi, de most kitörölt inputok törlése

        $deleting_ids = self::query()->whereNotIn('id', $input_ids)
            ->where('case_input_id', $input_id)->pluck('id');

        self::query()->whereIn('id', $deleting_ids)
            ->delete();

        Translation::query()->whereIn('translatable_id', $deleting_ids)->where('translatable_type', self::class)->delete();
        // új input valuek létrehozása
        if (! $request->input('new')) {
            return;
        }

        if ($request->new === null) {
            return;
        }
        // új elemek hozzáadása
        foreach ($request->new['name'] as $key => $value) {
            // case input létrehozása
            $case_input_value = new self;
            $case_input_value->value = $value;
            $case_input_value->case_input_id = $input_id;

            if (array_key_exists('permission', $request->new)) {
                $case_input_value->permission_id = $request->new['permission'][$key];
            }

            if (array_key_exists('contract_holder_id', $request->new)) {
                $case_input_value->contract_holder_id = $request->new['contract_holder_id'][$key];
            }

            $case_input_value->save();

            // fordítás mentése
            foreach ($languages as $language) {
                if ($request->new_translations[$language->id][$key]) {
                    Translation::query()->updateOrCreate([
                        'language_id' => $language->id,
                        'translatable_type' => self::class,
                        'translatable_id' => $case_input_value->id,
                    ], [
                        'value' => $request->new_translations[$language->id][$key],
                    ]);
                }
            }
        }
    }
}
