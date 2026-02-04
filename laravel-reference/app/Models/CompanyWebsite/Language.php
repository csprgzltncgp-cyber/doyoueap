<?php

namespace App\Models\CompanyWebsite;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyWebsite\Language
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCode($value)
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereName($value)
 * @method static Builder|Language whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Language extends Model
{
    protected $connection = 'mysql_company_website';

    protected $table = 'languages';

    protected $fillable = ['name', 'code'];
}
