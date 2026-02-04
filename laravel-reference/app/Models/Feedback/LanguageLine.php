<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Feedback\LanguageLine
 *
 * @property int $id
 * @property string $group
 * @property string $key
 * @property array $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|LanguageLine newModelQuery()
 * @method static Builder|LanguageLine newQuery()
 * @method static Builder|LanguageLine query()
 * @method static Builder|LanguageLine whereCreatedAt($value)
 * @method static Builder|LanguageLine whereGroup($value)
 * @method static Builder|LanguageLine whereId($value)
 * @method static Builder|LanguageLine whereKey($value)
 * @method static Builder|LanguageLine whereText($value)
 * @method static Builder|LanguageLine whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LanguageLine extends Model
{
    protected $connection = 'mysql_feedback';

    protected $table = 'language_lines';

    public $translatable = ['text'];

    public $guarded = ['id'];

    protected $casts = ['text' => 'array'];
}
