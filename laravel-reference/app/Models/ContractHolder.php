<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * App\Models\ContractHolder
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Company> $companies
 * @property-read int|null $companies_count
 * @property-read Collection<int, ContractHolderInput> $inputs
 * @property-read int|null $inputs_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|ContractHolder newModelQuery()
 * @method static Builder|ContractHolder newQuery()
 * @method static Builder|ContractHolder query()
 * @method static Builder|ContractHolder whereCreatedAt($value)
 * @method static Builder|ContractHolder whereId($value)
 * @method static Builder|ContractHolder whereName($value)
 * @method static Builder|ContractHolder whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ContractHolder extends Model
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_x_contract_holder');
    }

    public function inputs(): HasMany
    {
        return $this->hasMany(ContractHolderInput::class);
    }

    public function companies(): ?Collection
    {
        return Company::query()->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', $this->id))->get();
    }
}
