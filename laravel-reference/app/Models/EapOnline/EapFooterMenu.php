<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\EapOnline\EapFooterMenu
 *
 * @property int $id
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapFooterMenuDocument> $eap_footer_menu_documents
 * @property-read int|null $eap_footer_menu_documents_count
 * @property-read Collection<int, EapTranslation> $eap_footer_menu_translations
 * @property-read int|null $eap_footer_menu_translations_count
 * @property-read mixed $first_translation
 *
 * @method static Builder|EapFooterMenu newModelQuery()
 * @method static Builder|EapFooterMenu newQuery()
 * @method static Builder|EapFooterMenu query()
 * @method static Builder|EapFooterMenu whereCreatedAt($value)
 * @method static Builder|EapFooterMenu whereId($value)
 * @method static Builder|EapFooterMenu whereSlug($value)
 * @method static Builder|EapFooterMenu whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapFooterMenu extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'footer_menus';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (self $menu): void {
            Storage::deleteDirectory('eap-online/footer-documents/'.$menu->id);

            foreach ($menu->eap_footer_menu_translations as $translation) {
                $translation->delete();
            }

            foreach ($menu->eap_footer_menu_documents as $document) {
                $document->delete();
            }
        });
    }

    public function getFirstTranslationAttribute()
    {
        return $this->eap_footer_menu_translations()->orderBy('created_at')->first();
    }

    public function eap_footer_menu_documents(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapFooterMenuDocument::class, 'footer_menu_id', 'id');
    }

    public function eap_footer_menu_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->first();
    }

    /**
     * @return bool[]
     */
    public function get_ready_languages(): array
    {
        $languages = EapLanguage::all();
        $ready_languages = [];

        foreach ($languages as $language) {
            $ready_languages[$language->code] = true;
            foreach ($this->eap_footer_menu_documents()->whereNull('translation_of')->get() as $document) {
                /** @var EapFooterMenuDocument $document */
                if (! $document->has_translation($language->id)) {
                    $ready_languages[$language->code] = false;
                }
            }
        }

        return $ready_languages;
    }
}
