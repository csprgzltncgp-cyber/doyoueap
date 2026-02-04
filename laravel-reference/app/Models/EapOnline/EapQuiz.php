<?php

namespace App\Models\EapOnline;

use App\Traits\EapOnline\CategoryTrait;
use App\Traits\EapOnline\VisibilityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapQuiz
 *
 * @property int $id
 * @property string $slug
 * @property int $input_language
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $eap_categories
 * @property-read int|null $eap_categories_count
 * @property-read Collection<int, EapQuestion> $eap_questions
 * @property-read int|null $eap_questions_count
 * @property-read Collection<int, EapResult> $eap_results
 * @property-read int|null $eap_results_count
 * @property-read EapThumbnail|null $eap_thumbnail
 * @property-read EapVisibility|null $eap_visibility
 * @property-read EapLesson|null $lesson
 * @property-read EapChapter|null $chapter
 * @property-read Collection<int, EapTranslation> $title_translations
 * @property-read int|null $title_translations_count
 *
 * @method static Builder|EapQuiz newModelQuery()
 * @method static Builder|EapQuiz newQuery()
 * @method static Builder|EapQuiz query()
 * @method static Builder|EapQuiz whereCreatedAt($value)
 * @method static Builder|EapQuiz whereId($value)
 * @method static Builder|EapQuiz whereInputLanguage($value)
 * @method static Builder|EapQuiz whereSlug($value)
 * @method static Builder|EapQuiz whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapQuiz extends Model
{
    use CategoryTrait;
    use VisibilityTrait;

    protected $connection = 'mysql_eap_online';

    protected $table = 'quizzes';

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($quiz): void {
            $quiz->eap_categories()->detach();
            $quiz->eap_questions()->each(function ($question): void {
                $question->delete();
            });
            $quiz->eap_results()->each(function ($result): void {
                $result->delete();
            });
            $quiz->eap_visibility()->delete();
            $quiz->title_translations()->delete();
        });
    }

    public function eap_categories(): BelongsToMany
    {
        return $this->belongsToMany(EapCategory::class, 'quiz_category', 'quiz_id', 'category_id');
    }

    public function eap_questions(): HasMany
    {
        return $this->hasMany(EapQuestion::class, 'quiz_id', 'id');
    }

    public function eap_results(): HasMany
    {
        return $this->hasMany(EapResult::class, 'quiz_id', 'id');
    }

    public function eap_visibility(): HasOne
    {
        return $this->hasOne(EapVisibility::class, 'resource_id', 'id')->type('quiz');
    }

    public function eap_thumbnail(): HasOne
    {
        return $this->hasOne(EapThumbnail::class, 'resource_id', 'id')->type('quiz');
    }

    public function title_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable', 'translatable_type', 'translatable_id', 'id');
    }

    public function hasTitleTranslation($language_id): bool
    {
        return $this->morphOne(EapTranslation::class, 'translatable', 'translatable_type', 'translatable_id', 'id')->where('language_id', $language_id)->exists();
    }

    public function lesson(): MorphOne
    {
        return $this->morphOne(EapLesson::class, 'lessonable');
    }

    public function chapter(): MorphOne
    {
        return $this->morphOne(EapChapter::class, 'chapterable');
    }

    public function hasCategory($category_id)
    {
        return $this->eap_categories()->where('category_id', $category_id)->exists();
    }

    public function hasMissingTranslation($language_id): bool
    {
        // title
        if (! $this->hasTitleTranslation($language_id)) {
            return true;
        }

        // questions and answers
        foreach ($this->eap_questions()->get() as $question) {
            if (! $question->hasTranslation($language_id)) {
                return true;
            }

            foreach ($question->eap_answers()->get() as $answer) {
                if (! $answer->hasTranslation($language_id)) {
                    return true;
                }
            }
        }

        // results
        foreach ($this->eap_results()->get() as $result) {
            if (! $result->hasTranslation($language_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool[]
     */
    public function getReadyLanguages(): array
    {
        $languages = EapLanguage::all();
        $ready_languages = [];

        foreach ($languages as $language) {
            $ready_languages[$language->code] = true;

            if (! $this->hasTitleTranslation($language->id)) {
                $ready_languages[$language->code] = false;
            }

            foreach ($this->eap_questions()->get() as $question) {
                if (! $question->hasTranslation($language->id)) {
                    $ready_languages[$language->code] = false;
                }

                foreach ($question->eap_answers()->get() as $answer) {
                    if (! $answer->hasTranslation($language->id)) {
                        $ready_languages[$language->code] = false;
                    }
                }
            }

            foreach ($this->eap_results()->get() as $result) {
                if (! $result->hasTranslation($language->id)) {
                    $ready_languages[$language->code] = false;
                }
            }
        }

        return $ready_languages;
    }

    public function getMissingTranslationsNumber(): int
    {
        $missing_translations = 0;
        $languages = EapLanguage::all();

        // title
        foreach ($languages as $language) {
            if (! $this->hasTitleTranslation($language->id)) {
                $missing_translations++;
            }
        }

        // questions and answers
        foreach ($this->eap_questions()->get() as $question) {
            foreach ($languages as $language) {
                if (! $question->hasTranslation($language->id)) {
                    $missing_translations++;
                }
            }

            foreach ($question->eap_answers()->get() as $answer) {
                foreach ($languages as $language) {
                    if (! $answer->hasTranslation($language->id)) {
                        $missing_translations++;
                    }
                }
            }
        }

        // results
        foreach ($this->eap_results()->get() as $result) {
            foreach ($languages as $language) {
                if (! $result->hasTranslation($language->id)) {
                    $missing_translations++;
                }
            }
        }

        return $missing_translations;
    }
}
