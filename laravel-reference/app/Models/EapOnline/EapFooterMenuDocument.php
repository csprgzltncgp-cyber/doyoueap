<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\EapOnline\EapFooterMenuDocument
 *
 * @property int $id
 * @property int $footer_menu_id
 * @property int $language_id
 * @property int|null $translation_of
 * @property string|null $path
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapFooterMenu|null $eap_footer_menu
 * @property-read EapLanguage $eap_language
 *
 * @method static Builder|EapFooterMenuDocument newModelQuery()
 * @method static Builder|EapFooterMenuDocument newQuery()
 * @method static Builder|EapFooterMenuDocument query()
 * @method static Builder|EapFooterMenuDocument whereCreatedAt($value)
 * @method static Builder|EapFooterMenuDocument whereFooterMenuId($value)
 * @method static Builder|EapFooterMenuDocument whereId($value)
 * @method static Builder|EapFooterMenuDocument whereLanguageId($value)
 * @method static Builder|EapFooterMenuDocument whereName($value)
 * @method static Builder|EapFooterMenuDocument wherePath($value)
 * @method static Builder|EapFooterMenuDocument whereTranslationOf($value)
 * @method static Builder|EapFooterMenuDocument whereUpdatedAt($value)
 * @method static bool has_translation($language_id)
 *
 * @mixin \Eloquent
 */
class EapFooterMenuDocument extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'footer_menu_documents';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $document): void {
            Storage::deleteDirectory(substr((string) $document->path, 0, strrpos((string) $document->path, '/')));

            $translations = self::query()->where(['footer_menu_id' => $document->footer_menu_id, 'translation_of' => $document->id])->get();

            foreach ($translations as $translation) {
                $translation->delete();
            }
        });
    }

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class, 'language_id', 'id');
    }

    public function eap_footer_menu(): BelongsTo
    {
        return $this->setConnection('mysql_eap_online')->belongsTo(EapFooterMenu::class, 'footer_menu_id', 'id');
    }

    public function has_translation($language_id): bool
    {
        return self::query()->where(['footer_menu_id' => $this->footer_menu_id, 'language_id' => $language_id, 'translation_of' => $this->id])->exists();
    }

    public function get_translation($language_id)
    {
        return self::query()->where(['footer_menu_id' => $this->footer_menu_id, 'language_id' => $language_id, 'translation_of' => $this->id])->first();
    }

    public function has_description_translation($language_id): bool
    {
        return self::query()->where(['footer_menu_id' => $this->footer_menu_id, 'language_id' => $language_id, 'translation_of' => $this->id])->whereNotNull('description')->exists();
    }

    public function get_description_translation($language_id)
    {
        return self::query()->where(['footer_menu_id' => $this->footer_menu_id, 'language_id' => $language_id, 'translation_of' => $this->id])->whereNotNull('description')->first();
    }
}
