<?php

namespace App\Models\EapOnline;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapExpertDayOff
 *
 * @property int $id
 * @property string|null $from
 * @property string|null $to
 * @property int $expert_id
 * @property int $language_id
 * @property int $permission_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapLanguage|null $eap_language
 * @property-read User|null $expert
 *
 * @method static Builder|EapExpertDayOff newModelQuery()
 * @method static Builder|EapExpertDayOff newQuery()
 * @method static Builder|EapExpertDayOff query()
 * @method static Builder|EapExpertDayOff whereCreatedAt($value)
 * @method static Builder|EapExpertDayOff whereExpertId($value)
 * @method static Builder|EapExpertDayOff whereFrom($value)
 * @method static Builder|EapExpertDayOff whereId($value)
 * @method static Builder|EapExpertDayOff whereLanguageId($value)
 * @method static Builder|EapExpertDayOff wherePermissionId($value)
 * @method static Builder|EapExpertDayOff whereTo($value)
 * @method static Builder|EapExpertDayOff whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapExpertDayOff extends Model
{
    use HasFactory;

    protected $connection = 'mysql_eap_online';

    protected $table = 'expert_day_off';

    protected $guarded = [];

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class);
    }

    public function expert(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(User::class, 'expert_id', 'id');
    }
}
