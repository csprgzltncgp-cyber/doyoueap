<?php

namespace App\Models;

use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Document
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property int $language_id
 * @property int $country_id
 * @property string $visibility
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Country $country
 * @property-read Language $language
 *
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document query()
 * @method static Builder|Document whereCountryId($value)
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDeletedAt($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereLanguageId($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document whereText($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereVisibility($value)
 *
 * @mixin \Eloquent
 */
class Document extends Model
{
    protected $table = 'documents';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CountryScope);
        static::addGlobalScope(new LanguageScope);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public static function index()
    {
        if (! Auth::check()) {
            return [];
        }

        $auth_user_id = Auth::user()->id;
        if (Auth::user()->country_id == null) {
            $expert_country_id = DB::table('expert_x_country')->where('expert_id', $auth_user_id)->first();
            $country_id = $expert_country_id->country_id;
        } else {
            $country_id = Auth::user()->country_id;
        }

        $language_id = Auth::user()->language_id;

        if (Auth::user()->type == 'expert') {
            return DB::table('documents')->where(['language_id' => $language_id, 'country_id' => $country_id])
                ->where('visibility', 'LIKE', '%'.Auth::user()->type.'%')
                ->get();
        }

        return self::query()->where('country_id', $country_id)
            ->where('language_id', $language_id)
            ->where('visibility', 'LIKE', '%'.Auth::user()->type.'%')
            ->get();
    }

    public static function add($request): void
    {
        $visible = '';
        foreach ($request->visible as $value) {
            $visible .= $value.'-';
        }
        $visible = substr($visible, 0, strlen($visible) - 1);

        $document = new self;
        $document->visibility = $visible;
        $document->name = $request->name;
        $document->text = $request->text;
        $document->language_id = $request->language_id;
        $document->country_id = $request->country_id;
        $document->save();
    }

    public static function edit($id, $request): void
    {
        $visible = '';
        foreach ($request->visible as $value) {
            $visible .= $value.'-';
        }
        $visible = substr($visible, 0, strlen($visible) - 1);

        $document = self::query()->findOrFail($id);
        $document->visibility = $visible;
        $document->name = $request->name;
        $document->text = $request->text;
        $document->language_id = $request->language_id;
        $document->country_id = $request->country_id;
        $document->save();
    }
}
