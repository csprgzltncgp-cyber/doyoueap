<?php

namespace App\Http\Livewire\Admin\AffiliateSearch;

use App\Mail\AffiliateSearchCommentCreated;
use App\Models\AffiliateSearch;
use App\Models\AffiliateSearchCompletionPoint;
use App\Models\City;
use App\Models\Country;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public $affiliateSearch;

    public $newComment;

    public $newConnectedUser;

    protected $rules = [
        'affiliateSearch.description' => 'string',
        'affiliateSearch.deadline' => 'date',
        'affiliateSearch.deadline_type' => 'required',
        'affiliateSearch.to_id' => 'exists:users,id',
        'affiliateSearch.country_id' => 'exists:countries,id',
        'affiliateSearch.city_id' => 'exists:cities,id',
        'affiliateSearch.permission_id' => 'exists:permissions,id',
        'affiliateSearch.status' => 'required',
        'newComment' => 'required',
        'newConnectedUser' => 'exists:users,id',
    ];

    public function mount(AffiliateSearch $affiliateSearch): void
    {
        if (auth()->user()->type != 'admin' && auth()->user()->id != $affiliateSearch->from_id) {
            abort(403);
        }

        session()->put('affiliate-search-prev-url', url()->previous());

        $this->affiliateSearch = $affiliateSearch;
        $this->affiliateSearch->load(['comments', 'comments.user', 'from', 'to', 'attachments', 'country', 'city', 'affiliate_type']);

        $this->affiliateSearch->comments->where('user_id', '!=', auth()->id())->map(function ($comment): void {
            $comment->seen = true;
            $comment->save();
        });
    }

    public function render()
    {
        $admins = User::query()
            ->whereIn('type', ['admin', 'eap_admin', 'account_admin', 'affiliate_search_admin'])
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        $countries = Country::query()->orderBy('name')->get();
        $cities = City::query()->orderBy('name')->where('country_id', $this->affiliateSearch->country_id)->get();
        $permissions = Permission::query()->get();

        $connected_users = $this->affiliateSearch->connected_users()->get();

        $connectable_users = User::query()
            ->whereIn('type', ['admin', 'eap_admin', 'account_admin', 'affiliate_search_admin'])
            ->where('connected_account', null)
            ->whereNotIn('id', array_merge([$this->affiliateSearch->from_id, $this->affiliateSearch->to_id], $connected_users->pluck('id')->toArray()))
            ->get();

        return view('livewire.admin.affiliate-search.edit', ['admins' => $admins, 'countries' => $countries, 'cities' => $cities, 'permissions' => $permissions, 'connectable_users' => $connectable_users, 'connected_users' => $connected_users])->extends('layout.master');
    }

    public function updated($field, $value): void
    {
        if (in_array($field, ['newComment', 'newConnectedUser'])) {
            return;
        }

        $this->validateOnly($field, $this->rules);

        $this->affiliateSearch->update([
            Str::afterLast($field, '.') => $value,
        ]);
    }

    public function confirm(): void
    {
        $this->affiliateSearch->update([
            'completed' => true,
        ]);

        $this->emit('alert', ['message' => __('affiliate-search-workflow.confirm_success')]);
    }

    public function reopen(): void
    {
        $this->affiliateSearch->update([
            'status' => AffiliateSearch::STATUS_OPENED,
            'completed' => false,
        ]);

        if (AffiliateSearchCompletionPoint::query()->where('affiliate_search_id', $this->affiliateSearch->id)->where('user_id', $this->affiliateSearch->to_id)->exists()) {
            AffiliateSearchCompletionPoint::query()->where('affiliate_search_id', $this->affiliateSearch->id)->where('user_id', $this->affiliateSearch->to_id)->delete();
        }

        $this->emit('alert', ['message' => __('affiliate-search-workflow.reopen_success')]);
    }

    public function saveComment(): void
    {
        $this->validateOnly('newComment', $this->rules);

        $this->affiliateSearch->comments()->create([
            'user_id' => auth()->id(),
            'value' => $this->newComment,
        ]);

        if (config('app.env') === 'production' || config('app.env') === 'local') {
            Mail::to($this->affiliateSearch->to->email)->send(new AffiliateSearchCommentCreated($this->affiliateSearch, auth()->user(), $this->affiliateSearch->to));
        }

        $this->newComment = null;
        $this->emit('commentSaved');
        $this->affiliateSearch->load(['comments', 'comments.user']);
    }

    public function connectUser(): void
    {
        if (! $this->affiliateSearch->connected_users()->where('user_id', $this->newConnectedUser)->exists()) {
            $this->affiliateSearch->connected_users()->attach($this->newConnectedUser);
        }

        $this->newConnectedUser = null;
        $this->emit('userConnected');
    }

    public function detachConnectedUser($user_id): void
    {
        $this->affiliateSearch->connected_users()->detach($user_id);
    }

    public function setStatus($status_id): void
    {
        $this->validateOnly('newConnectedUser', $this->rules);

        $this->affiliateSearch->update([
            'status' => $status_id,
        ]);
    }

    public function backToList()
    {
        if (session()->get('affiliate-search-prev-url') == route(auth()->user()->type.'.affiliate_searches.edit', ['affiliateSearch' => $this->affiliateSearch])) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return redirect()->to(session()->get('affiliate-search-prev-url'));
    }

    public function save(): void
    {
        $this->emit('alert', ['message' => __('common.edit-successful')]);
    }
}
