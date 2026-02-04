<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CountryScope implements Scope
{
    /**
     * @return bool|null
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::user() === null) {
            return false;
        }

        $baseClass = class_basename($model);

        if (Auth::user()->all_country == 1 || str_contains((string) Auth::user()->type, 'admin')) {
            return false;
        }

        if ($baseClass == 'Country') {
            if (Auth::user()->type == 'expert') {
                $normal_countries = Auth::user()->expertCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
                $crisis_countries = Auth::user()->expertCrisisCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
                $countries = $normal_countries->merge($crisis_countries);

                $builder->whereIn('countries.id', $countries);
            } else {
                $builder->where('countries.id', Auth::user()->country_id);
            }
        } elseif ($baseClass == 'Company') {
            if (Auth::user()->type == 'expert') {
                $builder->whereHas('countries', function (Builder $query): void {
                    $normal_countries = Auth::user()->expertCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
                    $crisis_countries = Auth::user()->expertCrisisCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
                    $countries = $normal_countries->merge($crisis_countries);

                    $query->whereIn('countries.id', $countries);
                });
            } else {
                $builder->whereHas('countries', function (Builder $query): void {
                    $query->where('countries.id', Auth::user()->country_id);
                });
            }
        } elseif (Auth::user()->type == 'expert') {
            $normal_countries = Auth::user()->expertCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
            $crisis_countries = Auth::user()->expertCrisisCountries()->withoutGlobalScope(self::class)->get()->pluck('pivot.country_id');
            $countries = $normal_countries->merge($crisis_countries);
            $builder->whereIn('country_id', $countries);
        } else {
            $builder->where('country_id', Auth::user()->country_id);
        }

        return null;
    }
}
