<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UsedCaseId
 *
 * @property int $id
 * @property string $case_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|UsedCaseId newModelQuery()
 * @method static Builder|UsedCaseId newQuery()
 * @method static Builder|UsedCaseId query()
 * @method static Builder|UsedCaseId whereCaseId($value)
 * @method static Builder|UsedCaseId whereCreatedAt($value)
 * @method static Builder|UsedCaseId whereId($value)
 * @method static Builder|UsedCaseId whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UsedCaseId extends Model
{
    use HasFactory;

    protected $fillable = ['case_id'];
}
