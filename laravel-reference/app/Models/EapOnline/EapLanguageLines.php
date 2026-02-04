<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapLanguageLines
 *
 * @property int $id
 * @property string $group
 * @property string $key
 * @property array $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EapLanguageLines newModelQuery()
 * @method static Builder|EapLanguageLines newQuery()
 * @method static Builder|EapLanguageLines query()
 * @method static Builder|EapLanguageLines whereCreatedAt($value)
 * @method static Builder|EapLanguageLines whereGroup($value)
 * @method static Builder|EapLanguageLines whereId($value)
 * @method static Builder|EapLanguageLines whereKey($value)
 * @method static Builder|EapLanguageLines whereText($value)
 * @method static Builder|EapLanguageLines whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapLanguageLines extends Model
{
    public $translatable = ['text'];

    public $guarded = ['id'];

    protected $connection = 'mysql_eap_online';

    protected $table = 'language_lines';

    protected $casts = ['text' => 'array'];

    public static function boot(): void
    {
        parent::boot();
    }
}
