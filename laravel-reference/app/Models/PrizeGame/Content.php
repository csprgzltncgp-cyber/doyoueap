<?php

namespace App\Models\PrizeGame;

use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapLanguage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\PrizeGame\Content
 *
 * @property int $id
 * @property int $language_id
 * @property int|null $company_id
 * @property int|null $country_id
 * @property int $type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Answer> $answers
 * @property-read int|null $answers_count
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read Collection<int, Digit> $digits
 * @property-read int|null $digits_count
 * @property-read Image|null $image
 * @property-read EapLanguage $language
 * @property-read Collection<int, Question> $questions
 * @property-read int|null $questions_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read Type|null $type
 *
 * @method static Builder|Content newModelQuery()
 * @method static Builder|Content newQuery()
 * @method static Builder|Content query()
 * @method static Builder|Content whereCompanyId($value)
 * @method static Builder|Content whereCountryId($value)
 * @method static Builder|Content whereCreatedAt($value)
 * @method static Builder|Content whereId($value)
 * @method static Builder|Content whereLanguageId($value)
 * @method static Builder|Content whereTypeId($value)
 * @method static Builder|Content whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Content extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $guarded = [];

    protected $table = 'prizegame_contents';

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $content): void {
            if ($content->image()->exists()) {
                Storage::delete('eap-online/prizegame/images/'.$content->image->filename);
                $content->image->delete();
            }

            foreach ($content->sections()->get() as $section) {
                $section->delete();
            }

            foreach ($content->digits()->get() as $digit) {
                $digit->delete();
            }

            foreach ($content->questions()->get() as $question) {
                foreach ($question->answers()->get() as $answer) {
                    $answer->delete();
                }

                $question->delete();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Country::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    public function digits(): HasMany
    {
        return $this->hasMany(Digit::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function hasContentLike(): bool
    {
        $count = static::query()
            ->where([
                'language_id' => $this->language_id,
                'type_id' => $this->type_id,
                'company_id' => $this->company_id,
                'country_id' => $this->country_id,
            ])->count();

        return $count > 1;
    }
}
