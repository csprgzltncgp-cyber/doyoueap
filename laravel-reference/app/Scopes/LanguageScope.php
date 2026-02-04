<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class LanguageScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $baseClass = class_basename($model);
        if (Auth::user()->all_language == 1 || Auth::user()->type == 'production_admin' || Auth::user()->type == 'production_translating_admin' || Auth::user()->type == 'admin' || Auth::user()->type == 'account_admin' || Auth::user()->type == 'financial_admin' || Auth::user()->type == 'eap_admin' || Auth::user()->type == 'todo_admin' || Auth::user()->type == 'affiliate_search_admin' || Auth::user()->type == 'supervisor_admin') {
        } elseif ($baseClass == 'Language') {
            $builder->where('languages.id', Auth::user()->language_id);
        } else {
            $builder->where('language_id', Auth::user()->language_id);
        }
    }
}
