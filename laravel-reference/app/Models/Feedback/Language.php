<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feedback\Language
 *
 * @property int $id
 * @property string $code
 * @property string $name
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCode($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereName($value)
 *
 * @mixin \Eloquent
 */
class Language extends Model
{
    protected $connection = 'mysql_feedback';

    protected $table = 'languages';

    protected $fillable = ['name', 'code'];

    public $timestamps = false;
}
