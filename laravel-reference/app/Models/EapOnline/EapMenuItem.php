<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\MenuItem
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|MenuItem newModelQuery()
 * @method static Builder|MenuItem newQuery()
 * @method static Builder|MenuItem query()
 * @method static Builder|MenuItem whereCreatedAt($value)
 * @method static Builder|MenuItem whereId($value)
 * @method static Builder|MenuItem whereName($value)
 * @method static Builder|MenuItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapMenuItem extends Model
{
    use HasFactory;

    protected $connection = 'mysql_eap_online';

    protected $table = 'menu_items';
}
