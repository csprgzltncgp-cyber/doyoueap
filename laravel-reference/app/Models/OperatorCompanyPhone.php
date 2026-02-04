<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\OperatorCompanyPhone
 *
 * @property int $id
 * @property int $operator_data_id
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OperatorData|null $operatorData
 *
 * @method static Builder|OperatorCompanyPhone newModelQuery()
 * @method static Builder|OperatorCompanyPhone newQuery()
 * @method static Builder|OperatorCompanyPhone query()
 * @method static Builder|OperatorCompanyPhone whereCreatedAt($value)
 * @method static Builder|OperatorCompanyPhone whereId($value)
 * @method static Builder|OperatorCompanyPhone whereOperatorDataId($value)
 * @method static Builder|OperatorCompanyPhone wherePhone($value)
 * @method static Builder|OperatorCompanyPhone whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OperatorCompanyPhone extends Model
{
    protected $guarded = [];

    protected $table = 'operator_company_phones';

    public function operatorData(): BelongsTo
    {
        return $this->belongsTo(OperatorData::class);
    }
}
