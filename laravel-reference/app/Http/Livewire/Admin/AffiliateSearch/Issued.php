<?php

namespace App\Http\Livewire\Admin\AffiliateSearch;

use App\Models\AffiliateSearch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class Issued extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectDefault'));
        } else {
            $this->orderBy = ['id', 'desc'];
        }
    }

    public function render()
    {
        $affiliateSearches = AffiliateSearch::query()
            ->where('from_id', Auth::id())
            ->join('users', 'users.id', '=', 'affiliate_searches.to_id')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->select('affiliate_searches.*')
            ->get();

        if ($this->orderBy[0] == 'status') {
            $affiliateSearches = $this->statusOrder($affiliateSearches);
        }

        $affiliateSearches = $affiliateSearches->paginate($this->perPage);

        return view('livewire.admin.affiliate-search.issued', ['affiliateSearches' => $affiliateSearches])->extends('layout.master');
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectDefault', $value));
        $this->orderBy = explode(',', (string) $value);
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        $this->perPage = AffiliateSearch::query()
            ->where('from_id', Auth::id())
            ->orderByDesc('deadline')
            ->count();
    }

    public function statusOrder($affiliateSearches)
    {
        $affiliateSearches = $affiliateSearches->sortBy(function ($affiliateSearch) {
            if ($affiliateSearch->status == AffiliateSearch::STATUS_CREATED) {
                return 1;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_OPENED) {
                return 2;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_SEARCH_STARTED) {
                return 3;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_AFFILIATE_FOUND) {
                return 4;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_AFFILIATE_CONTACTED) {
                return 5;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_CONTRACT_SENT) {
                return 6;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_CONTRACT_SIGNED) {
                return 7;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD) {
                return 8;
            }
            if ($affiliateSearch->status == AffiliateSearch::STATUS_COMPLETED) {
                return 9;
            }
        });

        if ($this->orderBy[1] == 'asc') {
            return $affiliateSearches->values();
        }

        return $affiliateSearches->reverse()->values();
    }
}
